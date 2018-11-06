---
layout: post
title: 我是如何初始化 Mac 环境的
permalink: /page/provisioning-a-new-mac.html
type: programming
---

每个开发者可能都有自己的 dotfiles，我也不例外。这是对于一些复杂的命令行工具的备份，但对于自动化来说完全不够。

我希望在得到一台崭新 Mac 时，只需要执行自动化脚本完成80%的工作，并且按照文档配置剩余20%无法自动化的部分。

### 准备工作

为了让后续工作可以进行，我们先安装 macOS Command Line Tools。

```shell
xcode-select --install
```

当然，也可以选择下载安装 <https://developer.apple.com/download/more/>。

### Homebrew

[Homebrew](https://brew.sh) 是 macOS 中必备的包管理工具。通过 Homebrew 可以无需 root 一键安装各类软件，省去复杂的依赖管理和编译参数配置。

同时，Homebrew Cask 还可以安装非命令行编译类程序，也就是平时日常用的软件都可以用 Homebrew Cask 进行安装。

Homebrew 强大的能力让自动初始化的第一步成为可能。

所以第一步，我们先安装 Homebrew：

```shell
if test ! $(which brew); then
  echo "Installing homebrew..."
  /usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
fi
```

### Homebrew Bundler

Homebrew 不仅仅是一个包管理器，还具有软件依赖管理能力。通过 Homebrew Bundler 可以帮你解决所有软件依赖，包括官方的 formula 以及 cask，甚至还包括 Mac App Store（简称 mas）中的应用。我们只需要一个 `Brewfile`，就可以配置好所有需要的应用。

熟悉 Ruby 的人应该不太需要解释，毕竟 Ruby 自己就有 Bundler 这个东西。

Homebrew 默认就安装了 Homebrew Bundler。

如果你的安装列表已经足够干净，那么可以执行：

```shell
$ brew bundle dump
$ cat Brewfile

brew "git"
brew "wget"
brew "mas"
brew "p7zip"
...
```

来生成现有依赖，输出到 `Brewfile`。

如果想自己写也比较容易，`Brewfile` 是一种简单的 Ruby DSL，写起来比大部分配置文件都简单。常用到的命令主要有 `brew` `tap` `cask` `mas`。

这四条命令分别对应：

* `brew install`
* `brew tap`
* `brew cask install`
* 就是[之前文章中](https://crispgm.com/page/awesome-terminal-tools.html#mas)介绍过的 Mac App Store 命令行工具。

命令行类应用：

```ruby
brew "git"
brew "wget"
brew "zsh"
brew "vim"
```

非命令行类：

```ruby
cask "google-chrome" # Chrome 浏览器
cask "alfred"
cask "sublime-text"
cask "visual-studio-code"
```

非官方 Formula 应用：

```ruby
tap "heroku/brew"
brew "heroku"
tap "homebrew/cask-fonts"
cask "font-source-code-pro"
```

Mac App Store 上的应用：

```ruby
mas "WeChat", id: 836500024
```

因此，只要维护好 [Brewfile](https://github.com/crispgm/dotfiles/blob/master/Brewfile) 那么应用自动化安装就完全解决了。

只需要运行：

```shell
brew bundle
```

### zsh/oh-my-zsh

Zsh 也就是 Z Shell，比起更加原生的 Bash。Zsh 的命令补全、主题系统和插件系统等都更加强大[^1]。

但想自己进行配置，学习和开发成本都比较高，因此就需要 Oh My Zsh。

Oh My Zsh 是个开源社区驱动的项目，简单的说就是集成一些常见的 Zsh 配置、插件和主题。我们需要做的只是配置一些简单直观的参数。甚至说，什么都不做，也有一个默认的开箱即用的配置。

Oh My Zsh 的 README 中说到：

> Oh My Zsh will not make you a 10x developer...but you might feel like one.

这句话十分风趣。虽然用了 Oh My Zsh 效率不一定能提升10倍，但至少「自我感觉」是这样的 :)

Zsh 在上一节已经通过 Homebrew Bundler 安装了，这里需要设置成默认 shell：

```shell
# 把 zsh 加入 shell 列表
sudo sh -c 'echo /usr/local/bin/zsh >> /etc/shells'
# 设置 zsh 为默认 shell
chsh -s $(which zsh)
```

然后安装 Oh My Zsh：

```shell
sh -c "$(curl -fsSL https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"
```

### dotfiles

### 开发相关

### 应用配置

针对配置来说，应用可以分为四种：

* 云端配置：各种互联网公司的产品大多都是，不需要太关心配置同步的问题，只需要完成登录就好。比如：Chrome。
* dotfiles 风格配置：既然是 dotfiles 风格，那也可以用 dotfiles 的方式，直接提交到 dotfiles 中就好了。比如：Karabiner 和 Shadowsocksx-NG。
* 可同步配置：可以通过云端网盘进行同步，比如 Alfred 和 Dash。这种国内环境可以使用坚果云同步。
* 无解的：那就只能无解了。

### macOS 配置

macOS 配置其实已经很个性化了，我这里权当分享一下。

##### 触摸板

* 设置轻触触摸板 Tap to click
* 设置右键为触摸板右下角

##### 时间

* 自动切换时区
* 24小时
* 显示日期

##### Finder

* 打开时默认进入桌面
* 清除无用的标签和侧边栏项

##### Dock

### 最后

---

[^1]: [What is ZSH, and Why Should You Use It Instead of Bash?](https://www.howtogeek.com/362409/what-is-zsh-and-why-should-you-use-it-instead-of-bash/)