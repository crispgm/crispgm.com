#### What is YouCompleteMe

[YouCompleteMe](https://github.com/Valloric/YouCompleteMe) 是一个vim插件，简称为ycm，支持基于语义的代码补全和代码语法检查功能。

目前ycm支持5种程序语言:

    C/C++/Objective-C (c-family languages, 基于clang/llvm)
    Python (基于[Jedi](https://github.com/davidhalter/jedi)实现)
    C# (基于[OmniSharp](https://github.com/nosami/OmniSharpServer))

至于其他语言，ycm会调用vim omnifunc来匹配，因此不是很有必要使用ycm。

ycm虽然功能十分强大，不过编译安装较为复杂，本文基于Mac OSX 10.9 Maverics介绍一下如何安装成功爽上ycm。

#### Dependencies

* macvim 7.4，直接去macvim主页下载的就能用

* cmake，建议使用homebrew安装

* [vundle](https://github.com/gmarik/Vundle.vim)，安装起来很容易，按照github上面的介绍来就行

* Python，具体版本不详2.7应该总是没问题的

#### Installation

安装完vundle后，修改.vimrc

    vim ~/.vimrc

加入
    
    Bundle 'Valloric/YouCompleteMe'

保存重启vim后:BundleInstall，就开始_安装_了。这个安装并不是真正完全安装，只是把ycm的vim、python以及C++代码下载下来。

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
