---
layout: post
title: Mac Mail 无法退出
date: 2014/01/10 18:30:00 +0800
permalink: /page/mac-mail-cannot-quit.html
tags:
- Mac
- Mail
- Gmail
- Airmail
---

Mac Mail 在国内网络状况下，和 Gmail 一起总是会有点问题。
收件箱里选择删除一封 Gmail 邮件，并用```command+q```关掉 Mail。
如果删除 Gmail 失败的话就会报错，之后 Mail 就会处于一种卡死的状态，退不出来也进不去。
只能选择：

```
ps aux|grep Mail
kill -9 xxxx
```

这个事情遇多了，搞了个更简单粗暴的shell脚本

```
#!/bin/bash
PID=$(ps aux|grep $1|grep -v grep|tail -1|awk '{print $2}')
kill -9 $PID
```

直到后来，我试了下```zsh```的自动补全参数功能，输入```kill -9```之后再输入 Mail 就会自己智能获取```PID```，因此这个脚本瞬间没意义了。

又过了没多久，发现有```pgrep```这命令，于是没```zsh```一样轻取。

最后，我换成了 Airmail.

THE END
