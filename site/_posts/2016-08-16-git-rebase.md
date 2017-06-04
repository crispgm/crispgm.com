---
layout: post
type: programming
title: 开源项目中使用 git-rebase 合并提交
permalink: /page/git-rebase.html
tags:
- git
- GitHub
- OpenSource
---
最近，在 GitHub 上贡献代码时，常常是在本地提交 ```git commit``` 很多次后才发起 PR。亦或是在发起 PR 后，又因为功能、自动化测试、代码风格等原因，进行了几次修复性质的 commits。

当维护者完成 Code Review 之后，往往就会提出类似这样的要求：

* _LGTM after a quick rebase!_

* _Can you squash the git commits, otherwise LGTM._

这是维护者要求提交者使用 ```git rebase``` 将自己的所有提交合并成一条。

# 什么是 git-rebase

[git-rebase](https://git-scm.com/docs/git-rebase) 的定义是：

> git-rebase - Reapply commits on top of another base tip

rebase 的官方中文翻译是“衍合”，意思是在另一个起点上重新应用提交。深入的具体用法可以参考 [Git 官方文档](https://git-scm.com/book/zh/v1/Git-%E5%88%86%E6%94%AF-%E5%88%86%E6%94%AF%E7%9A%84%E8%A1%8D%E5%90%88)。

对于当前的应用场景，rebase 的意义是把提交合成一个，这样对于你提交的一个功能，只会有一个提交记录，便于维护者管理。

# 如何操作？

## 设置Editor

首先，要确定是 Terminal 的 ```EDITOR```。我选择设定为 vim。

修改 ```~/.zshrc```（非 zsh 用户可以用 ```~/.bashrc```），加入：

```
export EDITOR='vim'
```

## 执行 rebase

先数一数你提交过的数量，比如之前提交了5次：

```
git rebase -i HEAD~5
```

Git 会调起 vim，提示让你处理 commits：

```
pick f3e7c2e Add RIO medal count bot
pick 316f48a Add weekly template
pick 23c5c47 Move require to third party libs within implementations
pick 64cad0b Test coverage 100% for test/command
pick 030f64a Graceful require

# Rebase 2dfc82e..030f64a onto 2dfc82e (5 command(s))
#
# Commands:
# p, pick = use commit
# r, reword = use commit, but edit the commit message
# e, edit = use commit, but stop for amending
# s, squash = use commit, but meld into previous commit
# f, fixup = like "squash", but discard this commit's log message
# x, exec = run command (the rest of the line) using shell
# d, drop = remove commit
#
# These lines can be re-ordered; they are executed from top to bottom.
#
# If you remove a line here THAT COMMIT WILL BE LOST.
#
# However, if you remove everything, the rebase will be aborted.
#
# Note that empty commits are commented out 
```

## 处理提交

按 ```i``` 进入编辑模式，常用的操作有

```
p: 选取
r: 变更提交信息
s: 将提交信息合并到上一个提交中
```

最早的一条提交是必须保留的，可以选取或者变更提交信息，但不能选择合并。对于后面几条则可以进行合并。

```
pick f3e7c2e Add RIO medal count bot
s 316f48a Add weekly template
s 23c5c47 Move require to third party libs within implementations
s 64cad0b Test coverage 100% for test/command
s 030f64a Graceful require
```

> 注：用 r 修改提交信息时，不需要在这个里面改，直接保存退出 vim 会弹出专门的修改信息文件。

```wq``` 保存后，会进入合并信息页面，再次保存后完成 rebase。

## Push

进行完 rebase 后，直接执行 ```git push origin your-branch-name``` 会失败，这时千万不要按照提示进行 ```git pull```，否则会触发 merge。

直接使用 ```-f``` 参数进行强制 push：

```
git push -f origin your-branch-name
```

完成 push 后，GitHub 的 PR 中，几条提交就会被合并到了同一条中。
