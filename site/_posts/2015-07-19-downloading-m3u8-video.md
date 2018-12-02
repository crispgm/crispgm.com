---
layout: post
type: legacy
title: Apple iOS m3u8 媒体文件下载
date: 2015/07/19 13:43:00 +0800
permalink: /page/downloading-m3u8-video.html
tags:
- Apple
- iOS
- ts
- m3u8
- ffmpeg
---

## 背景

由于 Apple iOS 不支持 Adobe Flash 技术，所以在 iOS 上要使用 m3u8 进行多媒体和流媒体等的播放。如：微博视频。

对于好看的视频，有时候希望能够下载下来，具体方法请看本文介绍。

## 简介

m3u8 是 Apple iOS 流媒体使用的一种格式，它本质上是个文本的播放列表，实际的媒体文件是 MPEG2-TS 或者 AAC(Audio Only)。具体可以参考 Wikipedia 的英文介绍：[https://en.wikipedia.org/wiki/M3U](https://en.wikipedia.org/wiki/M3U)

### 获取 URL

首先要拿到 URL，方法是在 Chrome 上用 iPhone6 的 User-Agent 打开，然后在 Chrome 中找到，如下：

![]({{ "/image/video-url.png" | absolute_url }})

> http://us.sinaimg.cn/000MLkkJjx06TSSGESJF050d010000oz0k01.m3u8?KID=unistore,video&Expires=1437309945&ssig=kh06r9cH7F

### 获取 ts 文件

紧接着下载下来这个文件，用编辑器打开，就会看到里面的内容，是一堆 ts 格式的文件。

![]({{ "/image/m3u8-file-content.png" | absolute_url }})

### 下载源文件

把这些 ts 文件名跟 http://us.sinaimg.cn/ 拼在一起下载下来，就是被切成分片的视频源文件了。

![]({{ "/image/ts-file.png" | absolute_url }})

### 文件合并

最后，就是把这一堆文件合并在一起

有两种方法，一种比较粗暴，就是强型把文件的内容合并到一个文件中。

如：源文件是 1.ts 和 2.ts

那么就：

```shell
cat 2.ts >> 1.ts
```

这样，视频完全可以看，只不过因为 meta 信息还是 1.ts 的，会导致视频进度条显示不正确。

如果想优雅的合并，就需要专门的合并工具，强大开源工具 ffmpeg 是可以的。安装方法就不多讲了，同学们可以自己编译一个。对于 Mac 用户来说，可以去 [ffmpegmac](http://ffmpegmac.net/)直接下载编译好的二进制版本。

安装好 ffmpeg 后，执行下面的命令就可以完成合并：

```shell
ffmpeg -i "concat:1.ts|2.ts" -c copy output.ts
```

当然，ffmpeg 功能还很强大，比如保存成其它格式什么的。有兴趣可以深入探索下。
