---
layout: post
type: programming
title: 贴吧 BigPipe 实践 (nginx + hhvm)
date: 2015/06/26 16:23:00 +0800
permalink: /page/bigpipe-practice.html
tags:
- BigPipe
- nginx
- hhvm
- php
---

## 背景

随着 Web 页面的功能不断堆砌（其实我是极简主义者，但PM或者说是国内的风气，总是喜欢不断加入各类功能），页面需要的数据越来越多，串行连接后端的耗时自然是不断增大。常见的优化手段就是后端并行化，而对于前端来说，后端并行化只是降低了 response time，但用户最终看到页面的时间并没有减少。

Facebook 提出了 BigPipe 的方案，将页面功能分块，分成若干个 pagelet。Pagelet 的加载使用了 http 的 chunked 特性，采用类似 Pipeline 的方式进行前后端数据传输。浏览器端会首先获得一个框架层的 HTML/css，以及基础 JavaScript 代码。同时，后端也可以进行并行化，每个 pagelet 完成后，通过 flush 输出到浏览器。浏览器端的 JavaScript 基于事件机制，收到数据后进行渲染。

这样，前后端就可以都做到并行化，用户可以先看到部分页面内容，从而获得了更好的用户体验。目前，国外主要是 Facebook 应用了这项技术，而国内微博也通过 BigPipe 获得了不错的效果。

![Facebook-BigPipe]({{ "/image/fb-bigpipe.png" | absolute_url }})

图：Facebook 加载时的 timing，可以看到 waiting 时间（也就是后端响应时间 response time）明显低于 content download 耗时。

## 简单 Demo

```php
echo 'hello';
flush();
ob_flush();
sleep(1);
echo 'world';
```

这是一个最简单的 BigPipe demo，然而由于 `fastcgi_buffer` 的存在，并不能看到分段输出的效果。那么，我们把程序进行一下改动，用 `str_pad` 填充一些字符以达到 buffer。ps：吐槽一下 `str_pad` 这个函数名，明明 str 系列函数都是不带下划线的，如 `strlen`, `strcpy` 等，但这个函数却有下划线。str 系列函数表示：我们之间出现了叛徒！

```php
echo str_pad('hello', 10000, ' ');
flush();
ob_flush();
sleep(1);
echo str_pad('world', 10000, ' ');
```

进行字符填充后，BigPipe 效果显现了出来，hello 之后过1秒后会才会出现 world。由此可见，buffer 这块是个问题，后面会单独具体介绍科学优雅的解决方法。

## 实践

### 整体设计

BigPipe 的整体方案是需要具体实现环节分为如下几部分：

1. BigPipe 框架。包括前端和后端两部分，以及对于不支持 BigPipe 模式的流量启用的降级模式。此外，为了便于 SEO，对于搜索引擎 Spider 的抓取也要使用降级模式。
2. Pagelet 和 DataProvider 管理维护制度。这是一项管理上的措施，主要是为了管理 Pagetlet、DataProvider 以及其之间的依赖关系。
3. BigPipe 调试工具。由于在 BigPipe 开发模式中，后端开发负责 DataProvider，前端人员负责 Pagelet，双方需要调试工具进行独立开发调试。

### 潜在问题

1. 页面交叉调用过多，导致分段输出效果并不好
2. 前端的误调用会影响后端的响应时间
3. 后端性能优化需要前端配合

### 异步并行框架

由于贴吧现有框架本身并不支持纯异步调用，只支持阻塞并行的远程调用(ral_multi)，其响应时间为：

```
t = max(t1, t2, t3...)
```

框架本身需要升级，在升级完成前需要基于现有架构模拟纯异步，并且要在框架支持纯异步后，平滑对 DataProvider 透明地迁移成纯异步模式。

因此，BigPipe 并行框架采用异步-回调模式，通过状态机模拟异步过程。状态机会以深度优先遍历 DataProvider 以及其依赖的 DataProvider，并初始化成 `INITIAL` 状态。没有依赖的 DataProvider 会直接执行，进入 `EXECUTING` 状态。当一个有依赖的 DataProvider 的依赖已经全部处于 `READY` 状态时，则会同一般的 DataProvider 一样 `execute` 执行。`execute` 函数中会有数据交互和业务逻辑处理，当处理完毕后需要主动调用 `ready` 函数将 DataProvider 自身置为 `READY`。

Pagelet 依赖的 DataProvider 都 `READY` 后，就会渲染页面。

### Buffer问题

由于“各路” buffer 的存在，如果包比较小的话 BigPipe 的 chunked 输出很可能会被 buffer 住。针对这种情况，一般来说有两种方式。

1. 使用 `str_pad` 这类函数进行填充，如：填充空格。永远将一次 flush 的数据填充到 `buffer_size`。
2. 调小 buffer，让数据更容易达到 `buffer_size`。
3. 关闭 buffer。

对于 Nginx 来说，会有 `proxy_buffer` 和 `fastcgi_buffer`。第一种方式，不用调整 buffer，但这种方式很不优雅，而且增加了带宽，并不是很合理。至于调小 buffer，这看起来是一个很好的思路，然而对于 gzip 过的数据来说，最小的 buffer 可能也比较大。因此，我们选择了关闭 `proxy_buffer` 和 `fastcgi_buffer`。

然而，这样带来了一个问题。线上运行中的 Nginx 1.4.4版本过低，关闭 `proxy_buffer` 的指令 `proxy_buffering off` 原生就支持。而关闭 `fastcgi_buffer` 的 `fastcgi_buffering` 需要1.5.6版本。所以首先，我们把 Nginx 版本升级到了1.7.8，重编译后上线。

```
Syntax: fastcgi_buffering on | off;
Default: fastcgi_buffering on;
Context: http, server, location
This directive appeared in version 1.5.6.
```

详见 [http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_buffering](http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_buffering)

```
# 旧版本的Nginx并不支持fastcgi_buffering
nginx: [emerg] unknown directive "fastcgi_buffering"
```

还有一个问题，就是我们并不想对所有请求都关闭 buffer。为了将影响面做得最小，我们只想关闭特定模块的 buffer。改 `nginx.conf` 固然可以实现，不过对于不支持嵌套 `if` 的 `nginx.conf` 来说这是个很不舒服的用法。

幸好，在升级的过程中，发现了一个刚好可以用 http header，用于关闭 buffer。

> Buffering can also be enabled or disabled by passing “yes” or “no” in the “X-Accel-Buffering” response header field. This capability can be disabled using the fastcgi_ignore_headers directive.

因此，配置上完全不用关闭 buffer，只需要在 php 代码中加 header 就好，顺利把 buffer 优雅关闭。

```php
header('X-Accel-Buffering: no');
```

### 效果评估

* TTFB时间减少56% (TTFB = time to first byte)
* 白屏时间减少59%
* 降低了局部刷新开发成本

### 其它总结

* 开发迁移时间超长，从立项到上线总共持续了半年，前端主要开发人员因为离职等原因换了三波
* 底层本质上还无法并发，优化效果远远不够彻底
* pagelet 交叉请求比较多，效果没有那么好
