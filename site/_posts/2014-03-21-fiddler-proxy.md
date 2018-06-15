---
layout: post
type: programming
title: Fiddler 网络调试工具
date: 2014/03/21 12:10:00 +0800
permalink: /page/fiddler-proxy.html
tags:
- Fiddler
- Web Debugging
---

## 什么是Fiddler

Fiddler 官方给 Fiddler 的描述语是 Web Debugging Proxy，也就是 Web 调试代理工具。

主要功能有：

* 截取 HTTP/HTTPS 请求

* 修改 Session

* 网络调试

* 安全测试

* 性能测试

本文，主要讨论前三项功能的使用方法，包括 Fiddler 的基础配置和功能以及使用内置 FiddlerScript 脚本进行复杂功能调试。

## 准备和配置

Fiddler 使用 C# 开发，支持 Windows XP 以上版本。对于 Linux 和 Mac 用户，有一个基于 Mono 的 Alpha 版本，经本人 Mac 实际测试，基本上算是不能用的状态。因此，不建议使用。

下载地址：[http://www.telerik.com/download/fiddler](http://www.telerik.com/download/fiddler)

安装完毕后，为了使用后续功能，建议安装 [FiddlerScript Editor](http://www.telerik.com/download/fiddler/fiddlerscript-editor) 插件，否则修改 FiddlerScript 会比较麻烦。

准备完毕后，请打开 Fiddler 进入 Tools->Fiddler Options，在 Connections 中勾选 Allow remote computers to connect，并重新启动 Fiddler。

## 网络请求抓取

完成了上述设置后，Fiddler 就已经可以抓取本地的网络请求。如果需要对手机 app 进行调试，则需要设置手机代理。

* iPhone  
    * 进入 Settings -> WLAN，单击你连接中的 Wi-Fi，在 HTTP PROXY 下选择 Manual，然后 Server 填写 Fiddler 所在机器在此 Wi-Fi 中的 IP 地址，Port 是默认的 8888。

* Android
    * 进入设置 -> Wi-Fi，长按你连接中的 Wi-Fi，点击修改网络网络，勾选高级选项，在 HTTP 代理中填写主机名和端口。

* PC 本地  
    * 用 ProxySwitchy 一类的插件，代理到 localhost:8888

选中请求后，可以查看 Headers、Cookies 和多种模式的（TextView/WebForms等）请求参数。

如果想查看 Response，则可以选中请求后点击 Inspectors 或者直接双击请求查看响应 Headers 和多种格式的响应内容。对于常见的数据结构，可以格式化展示 JSON 或 XML。

## Web Debugging

在进行调试时，我们可以使用 Fiddler 的几种调试功能：

* HOSTS，等同于操作系统的 HOSTS 文件。

* Filters，可以修改 Request 或 Response 的包体。

* AutoResponder，可以截获请求，直接将请求代理到指定 URL 进行响应。

* FiddlerScript，以 FiddlerScript(JScript.NET) 形式，实现 Filters 和 AutoResponder 的所有功能，并且可以定制 UI 界面。

## HOSTS

Fiddler 自带有 HOSTS 功能，跟 Windows 自带的 HOSTS 应该是一回事，不过在 Fiddler 中操作比较方便。

而是直接从菜单进入 Tools -> HOSTS...，勾选 Enable remapping of requests（此处省略数十字），就可以把请求线上服务通过 HOST 方式，代理到沙盒或者测试机。

## Filters

很多时候，客户端一次行为可能会产生多次 HTTP 请求，会造成很大的干扰，Filter 功能就可以选择性截取

首先进入 Filters 标签，勾选 Use Filters。

在Request Headers中，勾选 Show only if URL contains，并在后面的输入框中填写希望截取的 URL。同理，也可以勾选 Hide if URL contains，隐藏特定的请求。

除此之外，还可以直接修改 Request 或 Response 包体。

## AutoResponder

AutoReponder 可以截获 Request URL，并用其他 URL 进行 Respond。利用 AutoResponder，也可以实现将请求引导到指定机器的功能。

将原 URL 截获后，甚至可以直接修改 URL。截图请求时也可以有很多方式，包括URL正则匹配、包体匹配和 Headers 匹配等。

此外，AutoResponder 支持设置断点。可以在截获请求后，在断点处修改请求或者响应。

## FiddlerScript

FiddlerScript 就是通过 JScript.NET 语言(可以约等于 JavaScript)，直接修改 Request 和 Response，能够实现上述各种功能。对于熟练的专业用户来说，会更加方便灵活。

使用 FidderScript 建议安装第一节所说的插件，安装后就可以直接打开 FiddlerScript 标签，直接修改代码。

在 ```OnBeforeRequest``` 函数中修改：

* 添加 Headers

```
oSession.oRequest["http_net_type"] = "1";
```

* 添加 Cookies

```
// 增加预览机标识
oSession.oRequest.headers.Add("Cookie", "pub_env=1");
```

* 匹配 URL，防止添加的字段干扰到其他网页

```
if (oSession.uriContains("c.tieba.baidu.com")) {  
    // TODO
}
```

更多用法，请参考 [Fiddler Documentation](http://docs.telerik.com/fiddler/knowledgebase/fiddlerscript/modifyrequestorresponse)
