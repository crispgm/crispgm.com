---
layout: post
title: How to be a 100x Programmer?
permalink: /page/how-to-be-a-100x-programmer.html
type: programming
---

> A 10x programmer is, in the mythology of programming, a programmer that can do ten times the work of another normal programmer, where for normal programmer we can imagine one good at doing its work, but without the magical abilities of the 10x programmer.
>
> -- [The mythical 10x programmer](http://antirez.com/news/112)

Every programmer is a unique person, and he/she may improve his/her productivity in various ways.

As an old Chinese proverb said:

> 工欲善其事，必先利其器 (To do a good job, an artisan needs the best tools)

At least we should use some powerful tools to boost our productivity. Even if a tool may only increase 10x at most, it multiplies.

Let me show a example: 

* Tool No1 (10x)
* Tool No.2 (3x)
* Tool No.3 (2x)
* Tool No.4 (1.6x)
* Tool No.5 (1.1x)

Total: 10x * 2x * 1.5x * 3x * 1.1x = 100x

# Solutions

### zsh & oh-my-zsh

[zsh](https://www.zsh.org/) provides more power than bash and [oh-my-zsh](https://ohmyz.sh/) make it be powerful out-of-box, with great tab completion, clever aliases, and themes & plugins system.

Here is what happened after I typed `git <tab>`:

![zsh-git-completion]({{ "/image/zsh-git-completion.png" | absolute_url }})

Install zsh:

```shell
$ brew install zsh zsh-completions
$ chsh -s /bin/zsh
```

Install oh-my-zsh:

```shell
$ sh -c "$(wget https://raw.githubusercontent.com/robbyrussell/oh-my-zsh/master/tools/install.sh -O -)"
```

And Oh My Zsh indicates its BOOST officially.

> Oh My Zsh will not make you a 10x developer...but you might feel like one.

Now that it claims to make you "feel like" a 10x developer, I think it must be true.

**BOOST**: 10x, **PROGRESS: 10%**

### tmux

tmux is a terminal multiplexer. It is not only multiplexes terminals, but also multiplexes our productivity.

With tmux, we may open and keep multiple sessions in a terminal, open windows (actually tabs) inside a session, and even split windows to panes.

![tmux-example]({{ "/image/tmux-example.png" | absolute_url }})

And [gpakosz/.tmux](https://github.com/gpakosz/.tmux) is just like `oh-my-tmux`. If we do not want to setup tmux configuration by ourselves, checkout this one.

Install tmux:

```shell
$ brew install tmux
```

Install gpakosz/.tmux:

```shell
$ cd
$ git clone https://github.com/gpakosz/.tmux.git
$ ln -s -f .tmux/.tmux.conf
$ cp .tmux/.tmux.conf.local .
```

**BOOST**: 2x, **PROGRESS: 20%**

### fzf

[fzf](https://github.com/junegunn/fzf) is a command-line fuzzy finder.

It's a great replacement to the default <CTRL+R> but have more features, which is fuzzy and can be used with any lists.

![fzf-fuzzy-example]({{ "/image/fzf-fuzzy-example.png" | absolute_url }})

I think it a must-have one.

Install fzf:

```shell
$ brew install fzf
$ $(brew --prefix)/opt/fzf/install
```

Enable fzf in `.zshrc`:

```shell
[ -f ~/.fzf.zsh ] && source ~/.fzf.zsh
```

**BOOST**: 1.5x, **PROGRESS: 30%**

### Neovim

[Neovim](https://neovim.io/) a Vim-fork focused on extensibility and usability. If you are already a hardcore vim user, you may keep your choice. It makes no difference between the usabilities but provides modern GUIs, asynchronous job control, and API based on RPC. As a result, it is now really easy to develop a new plugin with popular programming languages compared to old-school VimScript.

Install Neovim:

```shell
$ brew install neovim
```

And we may "replace" vim with nvim in case you like:

```shell
alias vim="nvim"
```

HINT: Neovim do not use `.vimrc`, it support XDG base directories by default.

We now should setup in `~/.config/nvim/init.vim`. Just copy your `.vimrc` here, it mostly works.

And I recommend [Shougo/dein.vim](https://github.com/Shougo/dein.vim) as a plugin manager, which is "dark powered". Vim/Neovim plugins is another big topic. For myself, I use deoplete, NERDTree, and vim-airline.

**BOOST**: 3x, **PROGRESS: 90%**

### Themes & Colorschemes

Colors are important to programmer, because we actually stare at the screen, terminal, and editor for the most time, especially the coding period.

I recommend [Nord](https://www.nordtheme.com/) here. It is "an arctic, north-bluish color palette" and provides various ports officially. Thus, we can use Nord as theme or colorscheme in whatever libraries, tools, editors, and et cetera.

![nord ports]({{ "/image/nord-ports.png" | absolute_url }})

It could calm developer down, improve eyesight, and even prevent bugs. IMO, it increases productivity by about 10%, not that big but take effects on multiple places.

**BOOST:** 1.1x, **PROGRESS: 100%**

# Conclusion

The productivity of a developer can largely improves with an approachable toolset indeed. Get the tools ready is the first step. There are still 99% other parts to be concerned, especially our efforts.

Thanks for reading this article.
