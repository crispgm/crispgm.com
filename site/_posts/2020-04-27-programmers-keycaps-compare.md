---
layout: post
type: lifestyle
title: 程序员主题配色键帽对比
permalink: /page/programmers-keycaps-compare.html
---

近年来，GeekHack 上出现了多个受到程序员主题配色启发复刻的键帽。在国内讨论中，时常会看到有人觉得「码农风格键帽看起来都差不多」。不得不承认，或许是受到某种设计或者工艺限制的原因，这些主题风格在键帽上的呈现的确颇有相似之处。

但据我之前的使用经验，在实际的编辑器/IDE/终端使用时，不同的主题感官大部分还是很不一样的。个人感觉就是，显示器的色彩饱和度比较高，所以编辑器中的主题背景色会比较突出，让不同主题看起来会有很直观的不同。而在键帽上，就不会那么明显，有一部分「渲染美如画」到手后「翻车」的键帽或许也是这个原因。

这里，我希望串联起这些键帽在原来的编辑器中的样子。让广大之前没有接触过这些主题的键帽玩家，也对他们的背景有更为直观的认识。

下面，我会列出近年来几个程序员主题配色键帽：
- [GMK Solarized Dark](https://geekhack.org/index.php?topic=90192.0)
- [GMK Oblivion](https://geekhack.org/index.php?topic=99174.0)
- [GMK Nord](https://geekhack.org/index.php?topic=100646.0)
- [GMK Dracula](https://geekhack.org/index.php?topic=100727.0)
- [GMK Monokai Material](https://geekhack.org/index.php?topic=105160.0)

（排序以发布时间为准）

对比方式：
- 默认采用 GMK 版本
- 语言为 Go 和 JavaScript
- 编辑器为 Visual Studio Code (VSCode)
- 分别在 Go 和 JavaScript 下截图，Base 部分渲染图以及60%键盘渲染图

### Solarized Dark

[Solarized](https://github.com/altercation/solarized)创立于2011年3月15日，是一个有[维基百科词条](https://en.wikipedia.org/wiki/Solarized_(color_scheme))的主题，官方支持一些平台，不过已经数年没有更新。

Solarized 有两个版本，分别是 Dark 和 Light。其实很多编辑器主题也有类似的设置，甚至会自带4个不同版本。不过做成键帽的无一例外都是基于暗色模式的。

Solarized Dark 呈现为一种介于蓝绿之间的色彩，GMK Solarized Dark 看起来色彩更少更绿。由于稍显年代久远，我没有找到正面渲染图。

![]({{ "/image/keycaps/solarized-dark-in-go.png" | absolute_url }})
![]({{ "/image/keycaps/solarized-dark-in-js.png" | absolute_url }})
![]({{ "/image/keycaps/solarized-dark-set.png" | absolute_url }})

### Oblivion

Oblivion 在程序员主题中算是比较名不见经传的，直接搜索先搜出来的是键帽，GitHub 几个移植 repo 不超过30多颗星。不过在 VSCode 中，倒是一下就找到了。

Oblivion 在编辑器中给我的感受就是暗淡的「土色」背景，加上非常高亮的白色关键字部分，可以说比较欣赏不能。

但 GMK Oblivion 的设计比编辑器要优秀，它在字母部分背景色的选择上没有用编辑器中的背景，而是选用了更接近灰色的色彩。这让整体的观感出现了很大的变化，视觉效果远优于编辑器，感受不到那种扑面而来的「土色」，高亮白也直接被移除了。

除此之外，GMK Oblivion 还把字母区外的功能键，替换成了 Git 的命令，这点还比较有趣。大概是由于出的早，它还得到了「码农」的名字。

就我个人来说，Oblivion 大概是这几套键帽中，本身最冷门，且键帽效果跟主题相差最大的。我要是只看编辑器，大概不会想买这键帽。

![]({{ "/image/keycaps/oblivion-in-go.png" | absolute_url }})
![]({{ "/image/keycaps/oblivion-in-js.png" | absolute_url }})
![]({{ "/image/keycaps/oblivion-set.png" | absolute_url }})
![]({{ "/image/keycaps/oblivion-render.png" | absolute_url }})

### Nord

Nord 是一个极地深蓝冷淡风格的主题配色，在 GitHub 主 repo 有3500+个星，人气相当不错。据我本人考古发现，诞生于2016年9月。不同于其它更加社区性的主题，Nord 的作者从一开始就制定了官方的配色方案，并且尽可能移植支持了各种软件、编辑器、终端。当然，Nord 的作者没有精力覆盖到所有领域，GMK Nord 并不是 [Nord 官方](http://nordtheme.com/)设计的，属于移植项目。

GMK Nord 单从发布时间比 GMK Dracula 要早，但遭受了 Oblivion 和 Dracula 的前后夹击以及疫情等因素，团购时间不断延后，据说将于多灾多难的2020年5月1日开启GB。

我本人就是 Nord 的使用者和爱好者，还移植了 [Alfred 上的 Nord 主题](https://github.com/crispgm/alfred-nord)。我个人对于 GMK Nord 设计感到满意，它的基本还原了 Nord 那种低对比的深蓝色感觉。字母部分完全就是编辑器的感觉，novelties 和桌垫则完全复刻了 Nord 官方的图标和背景图。

对于我来说，就是必须冲，期待到货后用 Nord 键帽在 Nord 主题上写东西的场景。

![]({{ "/image/keycaps/nord-in-go.png" | absolute_url }})
![]({{ "/image/keycaps/nord-in-js.png" | absolute_url }})
![]({{ "/image/keycaps/nord-set.png" | absolute_url }})
![]({{ "/image/keycaps/nord-render.png" | absolute_url }})

### Dracula

Dracula 本身是一个知名程序员主题配色，诞生于2013年10月12日，是一个比较新的主题，在 GitHub 主 repo 有12000+个星，是这四个主题中最具人气的。同 Nord 一样，也有比较完善的[组织维护](https://draculatheme.com/)，对于100多个各种平台和软件提供了官方移植。

Dracula 的特点是鲜艳的色彩和对紫色的大范围使用，在相对平淡的深色程序员配色中属于比较骚气的，回过头看 GMK Future Funk 基本也是这几样颜色。我本身也比较喜欢只是觉得看久了容易视觉疲劳，所以没有用作日常使用。

GMK Dracula 的效果看起来完全具有 Dracula 的精髓，果然一炮走红，开团之后十分火爆。

ps: GMK Dracula 的 IC 贴也是浓浓的程序员风格，[推荐大家围观下](https://geekhack.org/index.php?topic=100727.0)。

![]({{ "/image/keycaps/dracula-in-go.png" | absolute_url }})
![]({{ "/image/keycaps/dracula-in-js.png" | absolute_url }})
![]({{ "/image/keycaps/dracula-set.png" | absolute_url }})
![]({{ "/image/keycaps/dracula-render.png" | absolute_url }})

### Monokai

Monokai 的作者本身就叫 [Monokai](https://monokai.nl/)，这是一个非常有人气的主题。虽然 Monokai 没有严格意义上的官方 repo，不好统计星的数量，但我比较确定 Monokai 的使用者一定很多，甚至很可能比 Dracula 多。Sublime Text 等老牌编辑器的默认主题就是 Monokai，这给它积累了不少人气，我在发现 Nord 之前也是在用 Monokai。

Monokai 不像 Nord 和 Dracula，主题的移植更多的依靠社区贡献，各平台都有非官方移植，并且也衍生出了很多相似的新主题。GMK Monokai Material 的作者 Oblotzky (也是 GMK Obvilion 的作者)就选择了一个 Monokai 的衍生主题——Monokai Material。

虽然这些衍生版其实跟 Sublime Text 上的版本已经有比较明显的不同了，但整体还是符合 Monokai 的特质。我个人觉得 Monokai 的特质就是彩色部分的色彩以及比较醒目的文本部分。

![]({{ "/image/keycaps/monokai-in-go.png" | absolute_url }})
![]({{ "/image/keycaps/monokai-in-js.png" | absolute_url }})
![]({{ "/image/keycaps/monokai-set.png" | absolute_url }})
![]({{ "/image/keycaps/monokai-render.png" | absolute_url }})

### 最后

以上仅供各位参考，喜欢不喜欢全在自己的口味，祝大家都能用上自己喜欢的主题和键帽。

由于这些主题和键帽官方资料并不多，如果对于主题和键帽相关背景有什么的错误的，欢迎大家指出。
