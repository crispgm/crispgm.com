---
layout: post
title:  我是如何把自己的 VPS 容器化的
permalink: /page/containerize-my-vps.html
tags:
- Docker
- Linode
- VPS
- DevOps
---
Docker 近期后端最火的技术，但在公司生产环境中并没机会使用。由于我的 VPS 主机有容器化的需求，借此机会部署并稍微体验了一下 Docker，谈谈事情的经过和自己的感受。

## 背景

一直以来，我使用 Linode VPS 作为个人主机，上面部署着网站。后来，由于国内网络环境的持续恶化，不得不搭建了 VPN 和 Shadowsocks。又由于个人网站样式和博客内容更新的频繁，将网站做成了通过 GitHub Webhook 自动化部署更新。

目前，VPS 上面搭了若干个服务：

* 个人网站（Jekyll 生成静态页面，certbot 生成 HTTPS 证书[^1]，nginx 提供服务）
* 基于 PPTP 的 VPN（由于 iOS 10 移除了 PPTP 的支持[^2]，故改为 L2TP）
* Shadowsocks Server
* 网站同步更新程序

但是，在运维和安全角度都一直存在着问题。

首先，VPS 的搭建是非自动化的，只能手动搭建，十分的麻烦。今年 7 月，VPS 曾经被黑客攻击，导致整个系统需要重做。手动搭建一次，需要几十分钟。尤其是 VPN 的搭建步骤十分多且复杂。

其次，由于历史原因上面的很多程序都是 root 账户运行，有较大的安全风险。之前就曾经发现网站部署程序存在 Shell 注入问题，幸好及时发现修复了。

最后，就是不同的程序有时会出现互相干扰的情况。如：VPN 使用的 iptables 有时会影响 Nginx 的对外端口；安装测试新程序时，会在系统产生中产生很多垃圾。

因此，我的需求是：

* 相对快捷的自动化搭建方式，可快速构建和恢复
* 不同程序在“沙盒”中运行，并做到资源隔离

## Why Docker

Docker 主要作用是容器化，它可以将一些服务“打包”成一个镜像（Image），然后在 Container 中运行。对于 Container 的描述是这样的：

> Container 技术是直接将一个应用程序所需的相关程序代码、函式库、环境配置文件都打包起来建立沙盒执行环境[^4]。

这样，从需求上讲，我需要自动化的运维能力和沙盒运行等需求都得到了完美的满足。

从技术趋势上讲，Docker 是目前容器界、云计算界甚至是业界最火的一项后端技术，也希望以此机会尝尝鲜。

由于现在的 Linode 东京机房机器非常稳定，且和北京的 ping 非常低。所以，我不会轻易放弃这个 Linode，希望能在 Linode 上搭建 Docker。还好，Linode 早在 2014 年就已经支持了 Docker[^3]。

## 安装 Docker

我的 Linux 镜像是 Ubuntu 14.04 LTS，安装方法非常简单[^5]，只需要执行：

```
curl -sSL https://get.docker.com/ | sh
```

PS：起初，我使用的是比较新的 Ubuntu 16.04 LTS 镜像，在此强烈不推荐。这个版本和 systemd 之间有某种问题。本想着轻松无脑地安装完毕，结果就这么在 `docker-engine` 处卡住了。找了好久，才在 StackOverflow 找到[解决方法](http://stackoverflow.com/questions/37227349/unable-to-start-docker-service-in-ubuntu-16-04/37640824#37640824)。

## 制作镜像

根据功能来说，需要起三个 Container 分别包含服务：

* Nginx, HTTPS 证书和更新部署程序
* Shadowsocks 服务
* L2TP VPN 服务

需要为每个服务制作一个镜像。其实，这些常见服务网络上都有制作好的镜像，这也是 Docker 软件分发优势的一个体现。为了让事情不那么快餐，我决定自制一个。为了简单，这里只详细说下 Shadowsocks 部分。其中，L2TP VPN 的镜像极为复杂，因此采用了 GitHub 上找的 [hwdsl2/docker-ipsec-vpn-server](https://github.com/hwdsl2/docker-ipsec-vpn-server)。

镜像是通过 `Dockerfile` 实现，是一种类似 Shell 的描述文件：

```
FROM ubuntu:trusty
MAINTAINER David Zhang <crispgm@gmail.com>
RUN apt-get update \
    && apt-get install -y python-pip \
    && pip install shadowsocks

COPY etc/shadowsocks.json /etc/shadowsocks.json

EXPOSE 2968

CMD /usr/local/bin/ssserver -c /etc/shadowsocks.json -d start
```

Dockerfile 很容易懂，主要是一些声明以及安装的一些过程，稍微阅读一下[指令文档](https://docs.docker.com/engine/reference/builder/)即可。

镜像基于 ubuntu:trusty，这个版本跟 Linode 上的系统版本并没有什么必然联系。Shadowsocks 在 pip 上，因此安装步骤异常简单。最后，把配置文件 `COPY` 进去，再 `EXPOSE` 端口。`CMD` 是 Container 启动后执行的命令。

## 启动 Container

完成编写后，就可以进行 Build：

```
docker build -t crisp/shadowsocks .
```

然后使用守护模式运行：

```
docker run --name ssserver -d -p 2968:2968 crisp/shadowsocks
```

执行 Docker 的 `ps` 指令后，发现 Container 起来就挂掉了：

```
$ docker ps
CONTAINER ID IMAGE COMMAND CREATED STATUS PORTS NAMES
```

Docker 在运行时必须有前台程序，不能单独就运行一个守护进程。而常见的 Server 程序大多都是以守护进程运行的，因此，采用了一个小技巧：

```
CMD /usr/local/bin/ssserver -c /etc/shadowsocks.json -d start \
    && tail -f /var/log/shadowsocks.log
```

这样，“容器化”改造顺利完成。

# 结论

比起更加底层的服务器虚拟化技术，Docker 是以应用程序为中心的虚拟化技术，它提供了标准可交付的软件。通过 Docker，我们可以快速构建出资源隔离的、安全的、不同的软件服务。

此外，它在持续集成和持续交付上也有着很大潜力，未来会持续跟进探索。

<hr>

[^1]: Enable HTTPS with Let's Encrypt. <https://crispgm.com/page/enable-https-with-letsencrypt.html>.
[^2]: Prepare for removal of PPTP VPN before you upgrade to iOS 10 and macOS Sierra. <https://support.apple.com/en-us/HT206844>.
[^3]: Docker on Linode. <https://blog.linode.com/2014/01/03/docker-on-linode/>.
[^4]: Container技术和服务器虚拟化是一样的技术吗？<http://dockone.io/question/5>.
[^5]: Docker Quick Reference. <https://www.linode.com/docs/applications/containers/docker-quick-reference-cheat-sheet>.
