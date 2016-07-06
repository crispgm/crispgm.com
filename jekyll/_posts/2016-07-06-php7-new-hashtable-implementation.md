---
layout: post
title: 『翻译』PHP 7 新的 Hash 表实现
date: 2016/07/06 21:17:00 +0800
permalink: /page/php7-new-hashtable-implementation.html
tags:
- PHP
- PHP7
- Internal
- Array
- Hash
---

# 声明

本文翻译自 PHP 开发组成员 Nikita Popov([@nikic](https://github.com/nikic)) 的博客，文章根据中文习惯做了一定的调整。

原文：[PHP's new hashtable implementation](https://nikic.github.io/2014/12/22/PHPs-new-hashtable-implementation.html)

# Disclaimer

This article is translated from a blog post of Nikita Popov([@nikic](https://github.com/nikic)),
in which I have done a small amount of adjustment based on the reading habit of Chinese.

Original Page: [PHP's new hashtable implementation](https://nikic.github.io/2014/12/22/PHPs-new-hashtable-implementation.html)

# 正文

大约三年前，我写过一篇[分析 PHP 5 数组内存消耗的文章](/page/how-big-are-php-arrays.html)。作为即将推出的 PHP 7 的工作的一部分，Zend 引擎很大一部分已经被重写，针对于更小的数据结构和更少的分配。在这篇文章中，我将对新的 Hash 表实现做一个概述，并展示为什么它比之前的实现更有效。

我使用一个脚本来测量内存使用情况，这个脚本会测试创建一个 100000 个不同整型数的数组所需要的内存：

```
$startMemory = memory_get_usage();
$array = range(1, 100000);
echo memory_get_usage() - $startMemory, " bytes\n";
```

下面的这张表展示了 PHP 5.6 和 PHP 7 在 32 位和 64 位系统中的内存占用对比：

```
        |   32 bit |    64 bit
------------------------------
PHP 5.6 | 7.37 MiB | 13.97 MiB
------------------------------
PHP 7.0 | 3.00 MiB |  4.00 MiB
```

换句话说，PHP 7 中的数组在 32 位系统中节省了 2.5 倍的内存，在 64 位（LP64）系统中节省了 3.5 倍，这相当的客观。

## Hash 表介绍

TODO