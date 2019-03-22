---
layout: post
type: programming
title: Homebrew 深度应用
permalink: /page/dive-in-homebrew.html
---

> macOS 上各种软件的管理，只要有 Homebrew 就足够了。

# 简介

Homebrew 是一款享有盛名的包管理工具，是 macOS 上包管理的事实(de facto)标准 。Homebrew 的意思是家酿啤酒。

它的官方 slogan 是 The missing package manager for macOS (or Linux)。Linuxbrew 已经在 Homebrew 1.9正式合入，成为 Homebrew 的“一等公民”[^1]。

Homebrew 最早的创建者是 Max Howell，目前的主要维护者是 Mike McQuaid。同 Jekyll 等开源项目一样，Homebrew 的核心维护者也是 GitHub 的员工。GitHub 对于员工亲自参加开源项目方面，做得一直不错。

顺带插播一条奇闻逸事：Homebrew 的创始人 Max Howell 活跃在开源社区，目前主要从事 Swift 相关的库开发。他在2015年初曾去 Google 面试，因为“白板”写算法题「翻转二叉树」失败，愤而发 tweet 讽刺：

> Google: 90% of our engineers use the software you wrote (Homebrew), but you can’t invert a binary tree on a whiteboard so fuck off. [^2]

### Why Homebrew

对于使用 Unix/Linux 类系统的用户往往都遇到过这些需求：
* 开源程序是源码分发，需要自己编译，但 `./configure` 参数超多，还要解决各种库的依赖
* 想把软件安装到用户目录，而不是系统目录（这样不需要 root 权限）
* 自己编译安装了软件，想删除时却不知道在哪里删除和删除哪些文件

各 Linux 发行版为了提升安装软件的体验，大多都提供了官方的包管理器，如 Ubuntu 的 aptitude (apt-get)。而苹果官方没有为 macOS 提供包管理器，市面上最好的解决方案就是 Homebrew。

目前 Homebrew 的中文内容大多以简单入门级教程为主，对于它的各类能力提之甚少，我希望在这里更全面的分享 Homebrew。

# 快速入门

网上基础的教程很多，我这里只简略的介绍下。对于基础功能类的深度用法，推荐这篇 [macOS 包管理工具 Homebrew 不完全指南](https://swiftcafe.io/post/home-brew)。

### 安装

开始安装前需要安装 macOS 命令行工具：
```shell
$ xcode-select —install
```

或者，在<https://developer.apple.com/download/more/>下载安装。

然后执行：
```shell
$ /usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
```

### 基础功能

##### 安装

```shell
$ brew install wget
```

完成安装后可以列出已安装内容：
```shell
$ brew list
wget
```

##### 升级

```shell
# 显示可以升级的包
$ brew outdated
cmake (3.13.4) < 3.14.0
yarn (1.13.0) < 1.15.2
youtube-dl (2019.03.09) < 2019.03.18

# 进行升级
$ brew upgrade cmake
```

##### 删除

```shell
$ brew uninstall wget
```

##### 安装桌面程序

```shell
$ brew cask install google-chrome
```

对于 Homebrew-cask 管理桌面程序，这里先不细说，后面会专门讲。

Homebrew 会把软件安装到 `/usr/local/Cellar`，并且通过软链链接到 `/usr/local/bin`。我们可以通过 `brew unlink` 和 `brew link` 删除或创建链接。

### 名词解释

Homebrew 把软件安装过程中的各种名词都进行了拟物化命名[^3][^4]，这些命名挺有意思，但对于大多数英语水平一般的人来说，有不少词汇有些生僻。

| 英文 | 直译 | 实际含义 |
|-----|-----|-----|
| formula(e) | 公式 | 安装包的描述文件，formulae 为复数 |
| cellar     | 地窖 | 安装好后所在的目录 |
| keg        | 小桶 | 具体某个包所在的目录，keg 是 cellar 的子目录 |
| bottle     | 瓶子 | 预先编译好的包，不需要现场下载编译源码，速度会快很多；<br>官方库中的包大多都是通过 bottle 方式安装 |
| tap        | (插入)水龙头 | 下载启用某个源 |
| cask       | 木桶 | 安装 macOS native 应用的扩展 |
| bundle     | 捆 | 描述 Homebrew 依赖的扩展 |

# 进阶用法

对于 Homebrew，我们很多时候往往都是上来就`brew search`或是`brew install`。缺什么安什么，没有充分的应用它的强大能力。

首先，Homebrew 虽为解决控制台程序而生，但它完全有能力（Cask）安装任何桌面软件。它还支持丰富的分类目软件库或第三方库。

除此之外，它还有一个打包安装或备份工具，可以把已安装的软件输出成 Brewfile。迁移到新电脑时，只要根据 Brewfile 运行 Homebrew bundle，就可以一键安装全部控制台、桌面和来自 Mac App Store 的程序（Mac App Store 上的软件通过 [mas](https://github.com/mas-cli/mas) 支持）。

### Tap

Tap 在 Homebrew 中我理解是个动词，指的是启用某个源。实际看了下发现，homebrew-services 和 homebrew-bundle 也已 tap 形式存在，因此可以认为 tap 实际指的应该是扩展（extension）。

Homebrew 默认情况下会自带：

- homebrew/core
- homebrew/cask
- homebrew/services
- homebrew/bundle

后两者并没有任何公式配方，而只是扩展程序。

除此之外的源，需要通过 `brew tap`来启用。如：

```shell
$ brew tap heroku/brew
$ brew install heroku
```

### Services

[Homebrew-services](https://github.com/Homebrew/homebrew-services) 是 Homebrew 的后台服务程序扩展，它基于 macOS 的 `launchctl`。后台服务类程序的安装依旧使用 Homebrew，在管理时可以使用 Homebrew-services 进行启动、重启和停止等操作。

```shell
$ brew install mysql
$ brew services start mysql
```

### Cask

[Homebrew-cask](https://github.com/Homebrew/homebrew-cask) 是 Homebrew 的 macOS Native 应用扩展，通过 cask 可以安装各类应用程序。

搜索 Cask 的方法和搜索普通包一样，但安装时需要加上 cask 指令：
```shell
$ brew search google-chrome
$ brew cask install google-chrome
```

##### cask-versions

Homebrew Cask 和 Homebrew 一样，默认库只维护最新版本，但有的时候我们还是需要用旧版的（比如：我只有 Dash 3 的 License，所以需要用 Dash 3 而不是最新的 Dash 4），那就可能需要使用 cask-versions。

```shell
$ brew tap homebrew/cask-versions
$ brew cask install dash3
```

##### cask-fonts

Homebrew 官方的字体源，比如 Mozilla 的开源字体 Fira Code：

```shell
$ brew tap homebrew/cask-fonts
$ brew cask install font-fira-code
```

### Bundle

Homebrew 不仅仅是一个包管理器，还具有软件依赖管理能力。通过 [Homebrew Bundle](https://github.com/Homebrew/homebrew-bundle) 可以帮你解决所有软件依赖，包括官方和第三方的 formula 以及 cask，甚至还包括 Mac App Store（简称 mas）中的应用。

Homebrew 默认就安装了 Homebrew Bundle。

我们只需要一个`Brewfile`，就可以配置好所有需要的应用。熟悉 Ruby 的人应该不太需要解释，毕竟 Ruby 自己就有 Bundler 这套东西，Brewfile 和 Gemfile 属于对应关系。

如果你的安装列表已经足够“干净”，那么可以执行`brew bundle dump`来生成现有依赖，输出到`Brewfile`：

```shell
$ brew bundle dump
$ cat Brewfile

brew "git"
brew "wget"
brew "mas"
brew "p7zip"
...
```

如果想自己写也比较容易，`Brewfile`是一种简单的 Ruby DSL，写起来比大部分配置文件都简单。只需要掌握一些常用到的命令，主要有`brew`, `tap`, `cask`和`mas`。

这四条命令分别对应：
* `brew install`
* `brew tap`
* `brew cask install`
* `mas install`

> 注：mas 也就是[之前文章中](https://crispgm.com/page/awesome-terminal-tools.html#mas)介绍过的 Mac App Store 命令行工具

命令行类应用：

```ruby
brew "git"
brew "wget"
brew "vim"
```

非命令行类：

```ruby
cask "google-chrome"
cask "alfred"
cask "visual-studio-code"
```

非官方 Formula 应用：

```ruby
tap  "homebrew/cask-versions"
brew "dash3"
tap  "homebrew/cask-fonts"
cask "font-source-code-pro"
```

Mac App Store 上的应用：

```ruby
mas "WeChat", id: 836500024
```

因此，维护好 [Brewfile](https://github.com/crispgm/dotfiles/blob/master/Brewfile) 就可以完美解决应用自动化安装，只需要运行：

```shell
brew bundle
```

# 扩展用法

### 提交 Formula

创建并提交一个 Formula 比较容易，官方教程非常详细[^5]。这里，我就不做单独介绍了。

### 自建 Tap

Homebrew 除了各种官方维护的源外，还支持自建软件库。默认使用 GitHub，需要以“homebrew-xxx”格式命名 Repo。Formula 或 Cask 文件需要存放在 Formula 或 Casks 二级目录中。

比如，Heroku 的自建库在 GitHub 上是`heroku/homebrew-brew`。通过`brew tap heroku/brew`就可以获取库里维护的包了。

## 私有 Tap

上面说到，Tap 默认维护在 GitHub，那么当我们想安装一些需要**保密**的软件时该怎么办呢？

答案是 Tap 支持指定 Git，因此也可以使用内网域名内的 Git：
```shell
$ brew tap yourcompany/brew git@git.yourcompany.com: yourcompany/homebrew-brew.git
$ brew install inhouse-app
```

# Tips

##### 禁用自动升级

Homebrew 自动升级触发概率很高，由于网络等问题，检查更新会很久有时会比较烦。可以通过环境变量禁用自动升级：

```shell
HOMEBREW_NO_AUTO_UPDATE=1 brew info mysql
```

##### 直接安装 Formula

Homebrew 的安装指令并非只支持名字，也可以用文件安装包括网络文件和本地文件。

```shell
# 本地
$ brew install blabla.rb
# 远程
$ brew install https://blablablabla.com/blabla.rb
```

##### 安装旧版软件

Homebrew 默认情况下只支持最新版软件安装，有些重要的版本会单独存在。但想安装一些小版本就得自己 DIY 了。

我所知有两种方法：
1. 在 GitHub 找到 Formula 所在Repo 中（默认为 [homebrew-core](https://github.com/Homebrew/homebrew-core/blob/master/Formula/)）的文件，把文件远程地址复制下来，用 `brew install` 安装。
2. 进入 homebrew-core 在 macOS 本地的路径，默认为 `/usr/local/Homebrew/Library/Taps/homebrew/homebrew-core`，`git checkout`到所在 commit，再用 `brew pin` 锁定这个文件的更新，然后进行安装。

---
[^1]: [Max Howell on Twitter: “Google: 90% of our engineers use the software you wrote (Homebrew), but you can’t invert a binary tree on a whiteboard so fuck off.”](https://twitter.com/mxcl/status/608682016205344768)
[^2]:  [https://github.com/Linuxbrew/brew/issues/612](https://github.com/Linuxbrew/brew/issues/612) 
[^3]: [Formula Cookbook — Homebrew Documentation](https://docs.brew.sh/Formula-Cookbook#homebrew-terminology)
[^4]: [Glossary of Homebrew Terms](http://tmr08c.github.io/devops/2016/10/24/homebrew-glossary.html)
[^5]: [Formula Cookbook — Homebrew Documentation](https://docs.brew.sh/Formula-Cookbook#an-introduction)
