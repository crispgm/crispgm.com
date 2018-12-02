---
layout: post
type: programming
title: YouCompleteMe Installation Guide on Mac
date: 2014/04/20 22:10:00 +0800
permalink: /page/you-complete-me-installation-guide.html
tags:
- vim
- Mac
- YouCompleteMe
---

## What is YouCompleteMe

[YouCompleteMe](https://github.com/Valloric/YouCompleteMe) 是一个 vim 插件，简称为 ycm，支持基于语义的代码补全和代码语法检查功能。

常用的补全插件如 ctags 和 AutoComplPop 等是基于文本的，类似于搜索时的 suggest 功能。不过，前者是事先扫描文本生成 tags 文件，后者是对当前打开的文件内容进行扫描。
如果 tags 或者打开的文件重不存在的内容，那就无法进行补全。此外，这种补全是基于单词的，所以补全的提示可能是变量、关键字、注释甚至是字符串里的内容。

所谓的基于语义的代码补全，简单的说就是通过对代码的语义进行分析。举个例子，对于对象或结构操作时，在输入`.`之后 ycm 可以补全提示对象的成员变量和成员函数等。
一般的成熟 IDE 如 Visual Studio 和 Eclipse 都具有这种能力。

目前 ycm 支持5种程序语言:
C/C++/Objective-C (基于 clang)，Python (基于 [Jedi](https://github.com/davidhalter/jedi)) 和 C# (基于 [OmniSharp](https://github.com/nosami/OmniSharpServer))。

至于其他语言，ycm 会调用 vim omnifunc 来匹配，因此不是很有必要使用 ycm。

ycm 虽然功能十分强大，不过编译安装较为复杂，本文基于 Mac OSX 10.9 Mavericks 介绍一下如何安装成功爽上 ycm。

### Dependencies

* vim 7.3.584+

* cmake，建议使用 homebrew 安装

* [vundle](https://github.com/gmarik/Vundle.vim)，安装起来很容易，按照 GitHub 上面的介绍来就行

* Python 2.6+

### Installation

安装完 vundle 后，修改 .vimrc

```
vim ~/.vimrc
```

加入

```
Bundle 'Valloric/YouCompleteMe'
```

保存重启 vim 后 `:BundleInstall`，就开始安装了。这个安装并没有包括编译，只是把 ycm 的 vim、python 以及 C++ 代码下载下来。

然后进入 YouCompleteMe 的目录开始编译，```--clang-completer``` 是启用 C family languages 提示(为的就是这功能，果断要加上)

```
cd ~/.vim/bundle/YouCompleteMe
./install.sh --clang-completer
```

到此，插件已经编译完毕，基本上可以使用了。不过此时进入编辑 cpp 文件，发现代码提示怎么还是基于 omnifunc 呢？原来缺少了一条配置。

### Configuration

编辑 ~/.vimrc 文件，加入

```
let g:ycm_global_ycm_extra_conf = '/Users/crisp/.vim/bundle/YouCompleteMe/cpp/ycm/.ycm_extra_conf.py'
```

重新进入 vim，已经可以正常对代码进行提示。

## In The End

![]({{ "/image/ycm-demo.png" | absolute_url }})

Congrats to myself!

ycm 提示出了对象 md 的成员函数(f 标识)和成员变量(m 标识)等信息。

对于 Mac 来说，由于作者(据我观察)就使用 Mac，而且 Mac OSX 版本比较统一，因此按照作者的 Mac OSX Super-quick installation 步骤基本可以顺利安装。

不过在其他 Linux 上可就不一样了，我在公司的开发机上编译 clang-completer 就一直没成功，等到有空的时候再去折腾下。

Good luck to everyone!
