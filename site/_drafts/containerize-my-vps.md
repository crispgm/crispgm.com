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
## 背景

一直以来，我都在使用 Linode VPS 作为个人主机，上面部署着网站和 VPN。后来，由于国内网络环境的持续恶化，不得不搭建了 Shadowsocks。又由于个人网站样式和博客内容更新的频繁，将网站做成了通过 GitHub Webhook 自动化部署更新。

目前，VPS 上面搭了若干个服务：

* 个人网站（Jekyll 生成静态页面，[certbot 生成 HTTPS 证书](https://crispgm.com/page/enable-https-with-letsencrypt.html)，nginx 提供服务）
* 女朋友的主页（同上）
* 基于 PPTP 的 VPN
* Shadowsocks Server
* 网站同步更新程序

但是，在运维和安全角度都一直存在着问题。

首先，由于历史原因上面的很多程序都是 root 账户运行，有较大的安全风险。之前就曾经发现网站部署程序存在 Shell 注入问题，幸好及时发现修复了。

其次，VPS 的搭建是非自动化的，只能手动搭建，十分的麻烦。今年 7 月，VPS 曾经被黑客攻击，导致整个系统需要重做。手动搭建一次，需要几十分钟。尤其是 VPN 的搭建步骤十分多且复杂。

## Why Docker

从需求上讲，我需要自动化的运维能力，因此理论上常见的 DevOps 工具都可以。但由于服务类型比较简单，甚至都没有数据库存储和动态语言后端，因此不需要特别复杂的运维能力。

Docker 主要作用是容器化，它可以将一些服务“打包”成一个镜像（Image），然后在 Container 中运行。这样，自动化构建的需求可以满足。

从技术趋势上讲，Docker 是目前容器界甚至是业界最火的一项后端技术，也希望以此机会尝尝鲜。

由于现在的 Linode 东京机房机器非常稳定，且和北京的 PING 非常低。所以，我不会轻易放弃这个 Linode，希望能在 Linode 上搭建 Docker。还好，Linode 早在 2014 年就[已经支持了 Docker](https://blog.linode.com/2014/01/03/docker-on-linode/)。

## 安装 Docker

我的 Linux 镜像是 Ubuntu 16.04 TLS，安装起来[比较简单](https://www.linode.com/docs/applications/containers/docker-quick-reference-cheat-sheet)，只需要执行：

```
curl -sSL https://get.docker.com/ | sh
```

本想着轻松无脑地安装完毕，结果就这么在 `docker-engine` 处卡住了。找了好久，才在 StackOverflow 找到[解决方法](http://stackoverflow.com/questions/37227349/unable-to-start-docker-service-in-ubuntu-16-04/37640824#37640824)。

创建 `/etc/systemd/system/docker.service.d/overlay.conf`，内容是：

```
[Service]
ExecStart=
ExecStart=/usr/bin/docker daemon -H fd:// -s overlay
```

然后执行：

```
sudo systemctl daemon-reload
systemctl show --property=ExecStart docker
sudo systemctl restart docker
```

## 制作镜像

根据功能来说，需要起三个 Container 分别包含服务：

* Nginx 和静态网站
* Shadowsocks 服务
* PPTP VPN 服务

需要为每个服务制作一个镜像。其实，这些常见服务网络上都有制作好的镜像，这也是 Docker 软件分发优势的一个体现。为了让事情不那么快餐，我决定自制一记。为了简单，这里只详细说下 Shadowsocks 部分。

镜像是通过 `Dockerfile` 实现：

```
FROM ubuntu:xenial
MAINTAINER David Zhang <crispgm@gmail.com>
RUN apt-get update \
    && apt-get install -y python-pip \
    && pip install shadowsocks`

COPY etc/shadowsocks.json /etc/shadowsocks.json

EXPOSE 2968`

CMD /usr/local/bin/ssserver -c /etc/shadowsocks.json -d start
```

Dockerfile 很容易懂，主要是一些声明以及安装的一些过程，稍微阅读一下[指令文档](https://docs.docker.com/engine/reference/builder/)即可。

镜像基于 ubuntu:xenial，也就是目前 Linode 上的系统版本。Shadowsocks 在 pip 上，因此安装步骤异常简单，一路 `apt-get` 就好。最后，把配置文件 `COPY` 进去，再 `EXPOSE` 端口。`CMD` 是 Container 启动后执行的命令，

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

## 未完待续

TODO