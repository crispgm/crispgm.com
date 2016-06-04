---
layout: post
title: Mac OSX 10.9 Mavericks 清除 DNS 缓存
date: 2014/05/03 16:23:00 +0800
permalink: /page/mac-dns-flush.html
tags:
- Mac
- OSX
- DNS
---

不说废话，正确的清除DNS缓存方式为：

```
dscacheutil -flushcache
sudo killall -HUP mDNSResponder
```
