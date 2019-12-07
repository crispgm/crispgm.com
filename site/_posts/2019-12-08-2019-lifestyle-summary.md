---
layout: post
type: lifestyle
title: 2019年度生活方式总结
permalink: /page/2019-lifestyle-summary.html
---

2018年我进行了第一次[年度个人盘点](/page/2018-lifestyle-summary.html)，虽然涉及的内容不多，只有软硬件类，但现在回看还比较有意义，可以知道每年自己大体的状态和思路。因此，今年我要进行一个更加广泛的个人生活方式总结。

## 硬件

### 加湿器

冬天天气太干燥了，于是在家里卧室和工位桌面均配置了加湿器。加湿器其实原理上并没有多少壁垒，飞利浦在加湿器除菌方面网络口碑不错，国外产品的造型（仅限HU5930）和品质感也比较好。

家里是HU5930。这款产品体积和容量都比较大，适合家里房间使用。有专门的水箱，灌水十分方便。而且造型比较美观，跟极简风格比较搭。家中其实没有那么干燥，主要用来再夜间给卧室加湿，减少早上鼻子的不适感。

工位是HU4801。这款图片看着不大，实际放办公桌上其实有些偏大，如果是小房间用这个也足够了。

### 蒸汽清洁机

看 YouTube 上汽车 detailing（此处不是“细节”的意思，应该翻译成汽车清洁）视频，印象最深刻的工具就是沙发清洁机和蒸汽清洁机。前者有个强力吸头，可以直接在沙发或者地毯上涂抹泡沫，然后吸干。或者则是高压蒸汽，清洁一些难以去除的污渍。

家里没有什么地毯和沙发需要清理，但对于蒸汽清洁机还是有需求的。于是购入了 Karcher 高温蒸汽清洁机，清洁面包机、电饭锅、灶台效果都超好，对于各种细缝能直接把灰尘、颗粒“喷”出来。

# 软件和服务

今年对于手机上的 app 以及服务，几乎没什么动作，原来用什么现在还在用。主要变化在于对于开发工具的尝试和改善。以往就对开发工具非常感兴趣，今年算是大面积革新了自己的工具链。

### Nord

[Nord](https://www.nordtheme.com/) 是一套配色主题。

原先在开发工具上尝试过若干配色主题，从 Sublime Text 默认的 Monokai，到 vim 上的 Zarniwoop，还有后来比较喜欢的 Tomorrow Night 系列等。

对于这些主题都是混用不成体系，而今年把所有能换主题的东西基本都统一成了 Nord：iTerm2、Sublime Text、Visual Studio Code、vim 等，甚至是 Arch Linux 上的主题等。

同时，我自己也移植制作了 [Alfred Nord](https://github.com/crispgm/alfred-nord) 主题。

![Alfred Nord Screenshot]({{ "/image/alfred-nord-screenshot.jpg" | absolute_url }})

### VSCode

全称 Visual Studio Code，不是传统的 Visual Studio IDE，而是一款基于微软开源技术的编辑器，在广大开发者心中或许已经成为了最佳图形化编辑器。

Nadella 上台后，微软推出了很多开源的东西，甚至拥抱了 Linux 社区。虽然 Windows 由于体验问题并不太容易撼动 macOS 在开发者心中的地位，但这些开发者友好的做法的确收获了不少开发者的青睐。

他成功证实了微软没有必要与开源开发者“为敌”，更应该做的是融入其中。而 VSCode 就是微软在融入开源社区后的最佳成果，也展现了微软多年的软件功力。

![VSCode Screenshot]({{ "/image/vscode-go-screenshot.png" | absolute_url }})

VSCode 有着丰富的基础功能和配置，允许开发者自己定制，并且有强大的插件系统和生态。采用社区中最流行的 TypeScript 和 Electron 技术开发，减少了代码贡献者和插件开发者的熟悉成本和壁垒。

### Command Line

__git__

git 很强大，强大到一般人可能只会 clone 然后 add、commit、push 三连。遇到问题后，可能只会祭出把文件备份走再 clone 回来接着干的粗暴方式。

就像新手入门 vim，只会用 i 进入编辑模式一顿狂改，然后 wq 退出。只发挥了工具1%的能力，其它什么也不会的感觉。

由于在 GitHub 和 GitLab 不断积累的一些操作经验，慢慢开始娴熟掌握了各种复杂的技能，比如 rebase、reset、cherry-pick 等。并且，通过一些网络资料以及《精通 Git》，掌握了一些真正的原理和高级用法。

我第一次稍微有种，我能基本掌控 git 的感觉。

__neovim__

今年从 vim 迁移到 neovim。其实对于我来说 vim 和 neovim 用起来没多大区别，更多的是稍微更认同一些 neovim 的社区模式，以及更丰富的插件生态。

一如既往，我不是 vim 的铁杆，只用 vim 做简单的编辑/修改操作。但既然用了，配置的也得舒服好用，这是我的配置：[crispgm/dotfiles - neovim](https://github.com/crispgm/dotfiles/tree/master/neovim)。

__Modern CLI Tools__

近年来涌现出一系列用户体验好、功能强大的现代（Modern）命令行工具，包括但不仅限于：fzf, ripgrep, fd 等。这些工具大多用新语言编写（Rust 或 Go），同时兼顾性能、UI 和用户体验。

同时，通过简化参数，降低了入门成本。默认参数或者几个简单参数就可以实现 GNU 那些“古董”工具们一大堆参数才能实现的功能。

### Reddit

Reddit 就是美国的贴吧。比起贴吧，他内容更科技，小众内容更加丰富有深度。

关注了一些关于极简主义 [/r/minimalism](https://www.reddit.com/r/minimalism) 和冥想 [/r/meditation](https://www.reddit.com/r/meditation) 的内容，以及比较感兴趣的技术类 [/r/unixporn、](https://www.reddit.com/r/unixporn、)、[/r/neovim](https://www.reddit.com/r/neovim)、[/r/archlinux](https://www.reddit.com/r/archlinux) 什么的。不过最长见识的，大概是机械键盘 [/r/MechanicalKeyboards](https://www.reddit.com/r/MechanicalKeyboards)，各种各样尺寸和配色的 DIY 键盘，实在太丰富了。

![Mechanical Keyboard]({{ "/image/reddit-mechanical-keyboard.png" | absolute_url }})

最后，感叹一下，中国的贴吧已经被百度失败的运营和内容审查弄得大不如前。看着它一步一步难用到现在这个样子，令人惋惜。

### Arch Linux

我本来沉浸于 macOS 优秀的开发和日常体验中很久了，但受到 Reddit 上内容的“引诱”，我开始对 Arch Linux 十分好奇。

众所周知，这类贴吧类网站总会有很多宝藏，但找不找得到还是很靠缘分的。而我的缘分来自 Reddit 的上的 [/r/unixporn](https://www.reddit.com/r/unixporn/)，最开始只是为了看看 vim 的配置而关注，但实际看到的是各种 Unix/Linux 桌面配置。与以往对 Ubuntu 那种橙色 UI 印象不同的是，在这里分享的都是很个性很漂亮的搭配，简直令我着迷。

后来知道，这种行为叫做 RICE。而他们往往使用 Arch Linux 作为操作系统的选择，配合上 i3、i3-gaps、bspwm 等 tiling window manager 进行构建。分享时大多会用 neofetch/pfetch 显示下系统信息，有时也会运行一个“黑客帝国”工具。

从上面的 git 和 vim 等工具就可以看出，我今年很爱折腾命令行工具。于是一拍即合开始折腾，买了一块新的SSD专门折腾 Linux，顺利的搭配出一版：

![Arch Linux]({{ "/image/rice-screenshot.jpg" | absolute_url }})

入坑后，我也写了一篇总结性博文——[The Fascinating Arch Linux RICE](/page/the-fascinating-arch-linux-rice.html)。

### Firefox

大概从前二、三年开始，美国互联网舆论（基本代表了美国左派）就开始攻击 Facebook，而 Facebook 自己也不争气，爆出各种侵犯隐私的事情。而本来就是白左大本营的 Google，也因为广告的商业模式侵犯用户隐私而备受质疑。很多“意见领袖”（比如 DHH）带头删除 Facebook、Uber 等 app，将 Google 替换成 DuckDuckGo。

而在浏览器行业，Chrome 也不可避免的中枪了。恰逢此时，Firefox 打起了隐私浏览器的旗号，成为了正确的浏览器。而我也难以免俗，在 Arch Linux 下不再使用 Chrome，在 macOS 上也减少 Chrome 的使用，把非工作内容浏览器换成了 Firefox。

不过，对于中国这个互联网相关法律不健全、基本完全不尊重个人隐私的国度，Google 的产品其实已经还不错了。因此，我们没有必要大规模弃用。

# 读书

今年看得书数量不少但内容比较杂，以至于到了年底有些不知道都看了些什么。综合来说，以后对于书的门类选择要更加专注，尤其不要乱尝试看一些历史类的书。一般人觉得好看的往往是历史演义或小说，很多真正的历史类书籍，可能真的就是流水账，或者是枯燥的记录，需要根据个人情况选择。

### 精通 Git

前面提到的书，英文版叫 Pro Git。

### 司马辽太郎之日本战国四部曲

虽说整体比较杂，但司马辽太郎这几本由于是按照 bundle 购入，所以主题还是比较明确的。分别是（按照阅读顺序）：
- 《新史太阁记》
- 《国盗物语：斋藤道三》
- 《国盗物语：织田信长》
- 《城塞》

这四部传记分别讲述了（按时间顺序）打破旧规、谋国成功从而掀起战国帷幕的斋藤道三；不循规蹈矩从而初成霸业，后于本能寺之变被明智光秀袭击身先死的织田信长；出身卑微善于揣摩领导得到织田信长赏识，之后为织田信长“复仇”后位极人臣的丰臣秀吉；和稳重、老谋深算、寿命长？，后来击败丰臣秀吉的儿子，最终夺取天下的德川家康。

在历史中，德川家康无疑是“胜利者”，从此进入了德川幕府的时代一直到明治维新。但对于司马辽太郎本人，德川家康却是这几人中他最为讨厌的。

看完了这几本书，基本弄清楚了日本战国时期的大概脉络。也促成了后来日本旅游时，专门探访了战国主战场之一的岐阜稻叶山城。

# 影音/动漫

### 铳梦

成年后第一次看漫画。偶然在飞机上看了电影《阿丽塔》的前部分，发现居然是 Cyber Punk 风格，于是对内容发展很有兴趣。搜索后得知是源自漫画《铳梦》，一部 Cyber Punk 风格的漫画。

《铳梦》画风和趣味性都不错，情节节奏比较快不冗长。最吸引我的当然是 Cyber Punk 的背景设定，跟《[「赛博朋克2077」的公司多恐怖？竟能动员百万军队](https://zhuanlan.zhihu.com/p/44622519)》中介绍的十分一致。

Cyber Punk 是反乌托邦的，我对于 Cyber Punk 中的“丛林法则”既不推崇也不向往。不过对于其中的生物机械学、半机械人（cyborg）以及其它各类未来科技非常感兴趣。

# 爱好
### 清洁🧹

把本来肮脏、杂乱的东西，清理干净，摆放整齐，是一种激励感和成就感很充足的事情。

买了扫地机器人、MUJI 清扫套装、蒸汽清洁机等工具，熟练掌握了灶台、水槽和马桶的清洁技术。
