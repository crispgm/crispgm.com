---
layout: post
title: 我是如何初始化 Mac 环境的
permalink: /page/provisioning-a-new-mac.html
type: programming
---

每个开发者或许都有自己的 dotfiles，我也不例外。这是对于一些复杂的命令行工具（vim、bash 等）配置的备份，并且衍生出了初始化一台新电脑环境的自动化脚本。

在 GitHub 可以[搜到很多](https://github.com/search?q=dotfiles)点赞很多的 dotfiles 项目，相关配置十分全面，并且几乎自动化了所有东西。这些项目做的都很好很有参考意义，但因为每个人实际的个性化配置需求很强，所以只能说进行参考，没法全盘使用。

一些无法自动化的东西也需要文档来指导如何手动配置，比如 [KrauseFx/new-mac](https://github.com/KrauseFx/new-mac) 就是完全通过文档指导。

我希望在得到一台崭新 Mac 时，只需要执行自动化脚本完成80%的工作，并且按照文档配置剩余20%无法自动化的部分。

### 准备工作

首先，先参照 [KrauseFx/new-mac](https://github.com/KrauseFx/new-mac) 撰写 [README](https://github.com/crispgm/dotfiles/blob/master/README.md) ，把步骤用文本的形式写好。即使自动化了，文档也是有必要的。

然后，为了让后续工作可以进行，我们先安装 macOS Command Line Tools。

```shell
xcode-select --install
```

也可以选择下载安装 <https://developer.apple.com/download/more/>。

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

Homebrew 不仅仅是一个包管理器，还具有软件依赖管理能力。通过 [Homebrew Bundler](https://github.com/Homebrew/homebrew-bundle)[^1] 可以帮你解决所有软件依赖，包括官方的 formula 以及 cask，甚至还包括 Mac App Store（简称 mas）中的应用。我们只需要一个 `Brewfile`，就可以配置好所有需要的应用。

Homebrew 默认就安装了 Homebrew Bundler。

如果你的安装列表已经足够干净，那么可以执行 `brew bundle dump` 来生成现有依赖，输出到 `Brewfile`：

```shell
$ brew bundle dump
$ cat Brewfile

brew "git"
brew "wget"
brew "mas"
brew "p7zip"
...
```

如果想自己写也比较容易，`Brewfile` 是一种简单的 Ruby DSL，写起来比大部分配置文件都简单。熟悉 Ruby 的人应该不太需要解释，毕竟 Ruby 自己就有 Bundler 这套东西，Brewfile 和 Gemfile 属于对应关系。

普通用户写 `Brewfile` 也并不困难，只需要掌握一些常用到的命令，主要有 `brew`, `tap`, `cask` 和 `mas`。

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
tap  "heroku/brew"
brew "heroku"
tap  "homebrew/cask-fonts"
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

Zsh 也就是 Z Shell，比起更加原生的 Bash。Zsh 的命令补全、主题系统和插件系统等都更加强大[^2]。

但想自己进行配置，学习和开发成本都比较高，因此就需要 Oh My Zsh。

[Oh My Zsh](https://github.com/robbyrussell/oh-my-zsh) 是个开源社区驱动的项目，简单的说就是集成一些常见的 Zsh 配置、插件和主题。我们需要做的只是配置一些简单直观的参数。甚至说，什么都不做，也有一个默认的开箱即用的配置。

Oh My Zsh 的 README [^3]中说到：

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

dotfiles 非常因人而异，对于我来说主要用 vim 和 zsh，因此主要配置这两个。脚本化比较简单，只需要 `cp`：

```shell
cp .bash_profile .zshrc .vimrc ~/
cp -r zarniwoop.vim/colors ~/.vim/
```

最后，启用 fzf：

```shell
$(brew --prefix)/opt/fzf/install
```

### 开发相关

也是因人而异，我平时主要用 Go 和 Ruby。

Go 需要配置：

```shell
mkdir -p ~/go
```

并在 `.zshrc` 加入：

```shell
export GOROOT=`brew --prefix go`/libexec
export GOPATH=~/go
export PATH=$PATH:$GOPATH/bin
```

Ruby 则要配置 `.gemrc` 和代理：

```shell
cp .gemrc ~/
gem install bundler
bundle config mirror.https://rubygems.org https://gems.ruby-china.com
```

编辑器用 Sublime Text 和 VS Code，增加快捷方式：

```shell
sudo ln -s /Applications/Sublime\ Text.app/Contents/SharedSupport/bin/subl ~/Applications/subl
sudo ln -s /Applications/Visual\ Studio\ Code.app/Contents/Resources/app/bin/code ~/Applications/code
```

### 应用配置

针对配置来说，应用可以分为四种：

* 云端配置：各种互联网公司的产品大多都是，不需要太关心配置同步的问题，只需要完成登录就好。比如：Chrome。
* dotfiles 风格配置：既然是 dotfiles 风格，那也可以用 dotfiles 的方式，直接提交到 dotfiles 中就好了。比如：Karabiner 和 Shadowsocksx-NG。
* 可同步配置：可以通过云端网盘进行同步，比如 Alfred 和 Dash。这种国内环境可以使用坚果云同步。
* 无解的：那就只能无解了。

具体需要视情况而定，还有一些稍微特殊的。比如说 VS Code，配置部分可以用 [Settings Sync](https://marketplace.visualstudio.com/items?itemName=Shan.code-settings-sync) 插件同步，插件的话可以通过命令行模式，也能做到自动安装。

```shell
# VS Code CLI 模式安装插件
code --install-extension arcticicestudio.nord-visual-studio-code
code --install-extension ms-vscode.Go
code --install-extension Shan.code-settings-sync
...
```

更多此类的东西，需要自己探索和总结。

### macOS 配置

macOS 的配置目前是手动，按理来说可以通过 `defaults` 进行配置。但目前还没有找到每一个的配置项，缺少的暂且手动解决，等 hack 完再来更新。

已有的可以参考 [mathiasbynens/dotfiles](https://github.com/mathiasbynens/dotfiles) 中的 `.macos` 文件[^4]，覆盖了大多数。

### 最后

为了测试这一套东西的确 work，我把家里的 Mac Book Air 2013 给重置了，创造了一个崭新的 Mac。经过实战测试的确可用 :)

把成品 [crispgm/dotfiles](https://github.com/crispgm/dotfiles) 分享出来，不求能直接用，也是给大家参考。

---

[^1]: [Homebrew/homebrew-bundle](https://github.com/Homebrew/homebrew-bundle)
[^2]: [What is ZSH, and Why Should You Use It Instead of Bash?](https://www.howtogeek.com/362409/what-is-zsh-and-why-should-you-use-it-instead-of-bash/)
[^3]: [robbyrussell/oh-my-zsh](https://github.com/robbyrussell/oh-my-zsh)
[^4]: [mathiasbynens/dotfiles .macos](https://github.com/mathiasbynens/dotfiles/blob/master/.macos)
