## 背景

由于Apple iOS不支持Adobe Flash技术，所以在iOS上要使用m3u8进行多媒体和流媒体等的播放。如：微博视频。

对于好看的视频，有时候希望能够下载下来，具体方法请看本文介绍。

## 简介

m3u8是Apple iOS流媒体使用的一种格式，它本质上是个文本的播放列表，实际的媒体文件是MPEG2-TS或者AAC(Audio Only)。具体可以参考Wikipedia的英文介绍：[https://en.wikipedia.org/wiki/M3U](https://en.wikipedia.org/wiki/M3U)

* 首先要拿到URL，方法是在chrome上用iPhone6的User-Agent打开，然后在chrome中找到，如下：

![](http://crispgm.com/image/video-url.png)

> http://us.sinaimg.cn/000MLkkJjx06TSSGESJF050d010000oz0k01.m3u8?KID=unistore,video&Expires=1437309945&ssig=kh06r9cH7F

* 紧接着下载下来这个文件，用编辑器打开，就会看到里面的内容，是一堆ts格式的文件。

![](http://crispgm.com/image/m3u8-file-content.png)

* 把这些ts文件名跟http://us.sinaimg.cn/拼在一起下载下来，就是被切成分片的视频源文件了。

![](http://crispgm.com/image/ts-file.png)

* 最后，就是把这一堆文件合并在一起

有两种方法，一种比较粗暴，就是强型把文件的内容合并到一个文件中。

如：源文件是1.ts和2.ts

那么就：

    cat 2.ts >> 1.ts

这样，视频完全可以看，只不过因为meta信息还是1.ts的，会导致视频进度条显示不正确。

如果想优雅的合并，就需要专门的合并工具，强大开源工具ffmpeg是可以的。安装方法就不多讲了，同学们可以自己编译一个。对于mac用户来说，可以去[ffmpegmac](http://ffmpegmac.net/)直接下载编译好的二进制版本。

安装好ffmpeg后，执行下面的命令就可以完成合并：

    ffmpeg -i "concat:1.ts|2.ts" -c copy output.ts

当然，ffmpeg功能还很强大，比如保存成其它格式什么的。有兴趣可以深入探索下。

