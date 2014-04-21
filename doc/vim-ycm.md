#### What is YouCompleteMe

[YouCompleteMe](https://github.com/Valloric/YouCompleteMe) 是一个vim插件，简称为ycm，支持基于语义的代码补全和代码语法检查功能。

常用的补全插件如ctags和AutoComplPop等是基于文本的，类似于搜索时的suggest功能。不过，前者是事先扫描文本生成tags文件，后者是对当前打开的文件内容进行扫描。
如果tags或者打开的文件重不存在的内容，那就无法进行补全。此外，这种补全是基于单词的，所以补全的提示可能是变量、关键字、注释甚至是字符串里的内容。

所谓的基于语义的代码补全，简单的说就是通过对代码的语义进行分析。举个例子，对于对象或结构操作时，在输入.之后ycm可以补全提示对象的成员变量和成员函数等。

一些成熟的IDE如Visual Studio和Eclipse都具有这种能力。

目前ycm支持5种程序语言:

* C-family languages, based on clang/llvm

    * C

    * C++

    * Objective-C

* Python, [Jedi](https://github.com/davidhalter/jedi)-based
    
* C#, [OmniSharp](https://github.com/nosami/OmniSharpServer))-based

至于其他语言，ycm会调用vim omnifunc来匹配，因此不是很有必要使用ycm。

ycm虽然功能十分强大，不过编译安装较为复杂，本文基于Mac OSX 10.9 Maverics介绍一下如何安装成功爽上ycm。

#### Dependencies

* vim 7.3.584+ 

* cmake，建议使用homebrew安装

* [vundle](https://github.com/gmarik/Vundle.vim)，安装起来很容易，按照github上面的介绍来就行

* Python 2.6+

#### Installation

安装完vundle后，修改.vimrc

    vim ~/.vimrc

加入
    
    Bundle 'Valloric/YouCompleteMe'

保存重启vim后_:BundleInstall_，就开始安装了。这个安装并没有包括编译，只是把ycm的vim、python以及C++代码下载下来。

然后进入YouCompleteMe的目录开始编译，_--clang-completer_是启用C family languages提示(为的就是这功能，果断要加上)

    cd ~/.vim/bundle/YouCompleteMe
    ./install.sh --clang-completer

到此，插件已经编译完毕，基本上可以使用了。不过此时进入编辑cpp文件，发现代码提示怎么还是基于omnifunc呢？原来缺少了一条配置。

#### Configuration

编辑~/.vimrc文件，加入

    let g:ycm_global_ycm_extra_conf = '/Users/crisp/.vim/bundle/YouCompleteMe/cpp/ycm/.ycm_extra_conf.py'

重新进入vim，已经可以正常对代码进行提示。

#### In The End

![](http://crispgm.github.io/image/ycm.png)

Congrats to myself! 

ycm提示出了对象md的成员函数(f标识)和成员变量(m标识)等信息。

对于Mac来说，由于作者(据我观察)就使用Mac，而且Mac OSX版本比较统一，因此按照作者的Mac OSX Super-quick installation步骤基本可以顺利安装。

不过在其他Linux上可就不一样了，我在公司的开发机上编译clang-completer就一直没成功，等到有空的时候再去折腾下。

Good luck to everyone!
