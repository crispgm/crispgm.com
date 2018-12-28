---
layout: post
title: 我的 HHKB 键盘演进式布局方案
permalink: /page/hhkb-evolutionary-way.html
type: lifestyle
---

自从工作时使用的 MacBook Pro 的「蝶式」键盘`T`键出现间歇性「连击」故障后，我就经常会打出 ~~retturn~~ 和 ~~ttype~~ 这样的错误拼写。在程序设计中，这会造成了很大的麻烦，毕竟这是语法错误。

于是，我选择入手了有线（我总是不相信无线设备）、无刻的 [HHKB Professional2 Type-S](http://www.pfu.fujitsu.com/hhkeyboard/type-s/) 作为替代。

HHKB 是个很有态度的键盘，接受了它往往代表着接受了一种「革命性」的键位设计，或许也是一种「生活方式」。我从不惧怕折腾，但我想说的是比起“革命性”（revolutionary）的方式，我更倾向于在此选择“演进式”（evolutionary）的方式。

下面我就分享下我使用 HHKB 的方式。

## 印象和布局

首先，拿到 HHKB 的第一印象是：

* 小，可以直接覆盖在 MacBook Pro 的键盘上
* 简，由于是无刻的按键没有字，正面只有右下角有标志
* 静，Type S 的 S 是 Silent，比起机械键盘，声音几乎都可以忽略

![small-size]({{ "/image/HHKB-small-size.jpg" | absolute_url }})

接着说布局，这也是 HHKB 为人所知的最大印象——通过独特的布局获得很高的效率。

![keyboard-setting]({{ "/image/HHKB-Pro-2-default-layout.png" | absolute_url }})

仔细观察了键位布局后，就会发现了很多不同：

1. 只有60键
2. 没有功能键 (F1, F2, ...)
3. `CapsLock` 被 `Control` 取代了
4. `Esc` 取代了重音号/波浪号的位置
5. `\` 在一般键盘 `=` 的位置
6. 没有方向键
7. 退格不在最右上角

针对这些*特性*，我的感受是：

1. 👍 这很极简，我喜欢
2. 👌 问题不大，因为 macOS 默认式关闭功能键的，平时也就用来调声音什么的
3. 😨 `Control` 在这个位置的确很舒服，但老的 `Control` 位置已经习惯了
4. 😨 这很坑。波浪号常用于切换到 `$HOME`，重音号常用于 Markdown
5. 🤷 还好，但其实编程中反斜杠用的也比较多
6. 😢 这可以说十分不方便
7. 😢 这和一般键盘不同

## 我的方案

这里我想简单说下背景，除去工作中的 MBP 外，我其实在家中还有一个配备了 Filco 键盘的 Windows 系统台式机，以及另外一台 Mac 笔记本。

我不担心我无法适应什么的，不爱折腾的人估计不会买这样另类的键盘，但需要考虑到同时使用其它设备。如果全面拥抱 HHKB 风格，我大概需要维护**3套**肌肉记忆。从 87 键 Windows 键盘到标准 Mac 键盘，再到 HHKB。而且 HHKB 作为工作中的键盘势必使用时间会远比另两种长，那么长久下来一定是对于 HHKB 更熟悉，在家中使用时反而效率会因为切换手感大大降低了。

并且，HHKB 的默认布局会使一些已有的 Mac 快捷键很难按。比如 `Command + 重音键` 切换同一个应用程序的不同窗口，原先是左手单手操作，现在要改成双手操作。

把 `Control` 挪到 `CapsLock` 虽然可以带来快乐和效率，但也会付出比较大的代价。我认为我可以以一种更加演进式的方式使用 HHKB。

所以“演进 Evolutionary” 是与“革命性 Revolutionary” 相对应的，我认为没有必要为了一款键盘抛弃长期依赖的肌肉记忆。

我的方式是使用 Karabiner 合理改键并且加入一些魔法。

## Karabiner

[Karabiner](https://pqrs.org/osx/karabiner/) 是 macOS 上最好最强大的改键工具，它为我解决 HHKB 的布局问题提供了思路。

#### 基本改动

* 把 `Control` 和 `Option` 交换，保持用普通键盘时的手势。HHKB 虽然在左下角没有按键，但由于 Mac 的左下角是 `Fn`，所以 `Option` 其实和 Mac 的 `Control` 位置差不多。`Option` 不那么常用，放在不喜欢的位置也没关系。
* 重音号/波浪号、退格和反斜杠三者进行交换。这样反斜杠和退格都回到熟悉的位置，重音号/波浪号的位置并没有得到改善，因此我会在后续说到怎么使用魔法优化。

![HHKB Option]({{ "/image/HHKB-option.jpg" | absolute_url }})
注：HHKB 的 `Option` 其实和 MacBook Pro 的 `Control` 位置几乎一样。

#### 魔法

魔法其实就是用 Karabiner 的组合改键，在保证原先按键功能的前提下，实现原来的效果。

* `Command + Esc` 改成 `Command + 重音键`，这样可以继续用原先的方式切换窗口。
* `Shift + Esc` 改成 `~`，这样可以继续用原先的方式输入波浪号。
* `Shift + Delete` 改成向前删除，这和 macOS 标准快捷键一致。

最后，记得**备份** `karabiner.json` 到你自己的 dotfiles，否则一旦丢失后果不堪设想。

**HHKB 无刻键盘让你的键盘只能自己使用，而一旦丢失 `karabiner.json` 恐怕你自己也没法正常使用。**

## 方向键

至于方向键的确没有更好的方式，HHKB 自带的 Fn 组合键整体还算可行。

## 其它 Tips

* 我的 DIP 选在 Lite Ext. Mode。比起 Mac Mode 只关闭了几个多媒体组合键。
* 禁用 `Fn + P`。`Fn + P` 就在组合的方向键旁边，一旦按错就会调屏幕亮度，很烦人。
* 设置 `disable_built_in_keyboard_if_exists`，便于放在 Mac 键盘上时不会按到下面。
* 弯头 USB 线。原装线会伸到屏幕上。

![USB Cable]({{ "/image/HHKB-usb-cable.jpg" | absolute_url }})

## 结论

HHKB 是个很好的键盘，但我觉得为了它而彻底改变肌肉记忆并不值得，Karabiner 可以帮助我采用更演进式的方式使用 HHKB。这里我给出我的 [karabiner.json](https://github.com/crispgm/dotfiles/blob/master/Karabiner/karabiner.json) 供参考。

这里我给出一些配置后的优点和缺点：

#### 优点

* 很小很极简，可以放在 MacBook Pro 上用
* 顺滑、舒适
* 只为你自己配置、使用，哈哈

#### 缺点

* 刚开始很容易输错密码
* 没有 Karabiner 生活不能自理
* 键盘唤醒时间很慢

最后，附上工作照：
![HHKB]({{ "/image/HHKB-at-work.jpg" | absolute_url }})

Happy Hacking!
