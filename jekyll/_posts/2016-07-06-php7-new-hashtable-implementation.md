---
layout: post
title: 『翻译』PHP 7 新 Hash 表实现
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

本质上，PHP 的数组其实是有序词典。也就是说，它们代表了一个由 key/value 对组成的有序列表，其中 key/value 映射由 Hash 表实现。

[Hash 表](http://en.wikipedia.org/wiki/Hash_table)是一个普遍的数据结构。从根本上，它解决了计算机只可以直接使用连续整数下标表达数组的，而程序员常希望使用字符串或者其它更复杂的类型作为 key 的问题。

Hash 表背后的概念很简单：字符串类型的 key 通过一个 Hash 函数，返回一个整数。这个整数作为一个“正常”数组的索引。问题是在于两个不同的字符串可能会得到同样的 Hash 值，因为可能的字符串组合实际上是无限的，而 Hash 却受到了整型的限制。所以，这些 Hash 表需要实现某种冲突解决机制。

业界有两种主要的冲突解决方法：开地址法，如果发生冲突，元素会被存储在另外一个索引中；链地址法，所以有同样 Hash 值的元素将会被存在一个链表中。PHP 使用了后者。

通常来说，Hash 表不会有明确的顺序：元素存储在数组底层的顺序依赖于 Hash 函数，并且相当的随机。但这个行为和 PHP 数组的语义不一致：如果你遍历一个 PHP 数组，你将会得到按插入顺序排列的元素。这意味着，PHP 的 Hash 表实现不得不加入额外的机制来记录数组元素的顺序。

## 旧的 Hash 表实现

我在这里只会简短的概述一下旧的 Hash 表实现，想获得更综合的理解可以阅读 PHP 内核书的 [Hash 表章节](http://www.phpinternalsbook.com/hashtables/basic_structure.html)。下图所示是一个 PHP 5 Hash 表的高级视图：

![](https://nikic.github.io/images/basic_hashtable.svg)

“冲突解决”链表中的元素是 buckets。每个 bucket 会被单独分配。图片隐藏了 buckets 中实际存储的值（只显示了 key）。值被存储在独立分配的 ```zval``` 结构中，结构有 16 bytes（32 位）或 24 bytes（64 位）。

另一件事就是图片没有显示冲突解决链表实际是一个双向链表（这样可以方便删除）。在冲突解决链表旁边，还有另一个双向链表存储用于存储数组元素的顺序。对于一个包含有以 key ```"a", "b", "c"``` 为顺序的数组，链表如下：

![](https://nikic.github.io/images/ordered_hashtable.svg)

所以，为什么旧的 Hash 表结构无论是内存消耗还是性能上都如此的低效？这有很多主要的原因：

* ```bucket``` 需要独立分配内存。内存分配慢而且需要额外增加 8 / 16 bytes。独立分配内存也意味着 ```bucket``` 会在内存中更分散，降低缓存效果。
* ```zval``` 也需要独立分配内存。同样的，内存分配慢而且会引起更多内存分配。除此之外，这还需要在每个 ```bucket``` 中存储一个指向 ```zval``` 的指针。因为旧的实现过度通用，它实际需要不止一个，而是两个指针。
* 双向链表需要为每个 ```bucket``` 存储 4 个指针，这单独就占用了 16 / 32 bytes… 除此之外，遍历链表是一件对缓存很不友好的操作。

新的 Hash 表实现设法解决（或至少是改善）这些问题。

## 新的 zval 实现

在进入实际的 Hash 表之前，我想先简单的看一下新的 ```zval``` 结构并且突出它和旧版的区别。```zval``` 结构定义如下：

```
struct _zval_struct {
    zend_value value;
    union {
        struct {
            ZEND_ENDIAN_LOHI_4(
                zend_uchar type,
                zend_uchar type_flags,
                zend_uchar const_flags,
                zend_uchar reserved)
        } v;
        uint32_t type_info;
    } u1;
    union {
        uint32_t var_flags;
        uint32_t next;       /* hash collision chain */
        uint32_t cache_slot; /* literal cache slot */
        uint32_t lineno;     /* line number (for ast nodes) */
    } u2;
};
```

你可以安全的忽略在定义中的 ```ZEND_ENDIAN_LOHI_4``` 宏，它只是用于在不同字节顺序的机器下保证可预计内存布局。

```zval``` 结构有三部分：第一个成员是值。```zend_value``` 联合体占用 8 bytes 用于存储不同类型的值，包括整型、字符串、数组等。具体存储什么依赖于 ```zval``` 的类型。

第二部分是 4 bytes 的 ```type_info```，包含有实际的类型（像 ```IS_STRING``` 或 ```IS_ARRAY```），并且有一些额外的提供这个类型信息的标识。比如，如果这个 ```zval``` 存储了一个对象，则类型的标识会说它是一个非常量、引用计数了的、可垃圾收集的、非复制类型。

最后 4 bytes 是 ```zval``` 结构通常未使用的（它只是明确的填充物，编译器会自动引入）。然而，在特殊的上下文中，这部分空间会被存储一些额外信息。比如：AST（抽象语法树）节点用它存储行号，虚拟机常量用它存储缓存槽的索引，Hash 表用它存储在冲突解决链表中的下一个元素 —— 最后这一部分对我们很重要。

如果你同之前的 ```zval``` 实现对比，一个最特别的不同是：新的 ```zval``` 结构不再存储引用计数 ```refcount```。这后面的原因是，```zval``` 不再被单独分配，而是被直接集成在任何存储它的地方（比如：Hash 表中的 ```bucket```）。

```zval``` 们本身不再使用引用计数，而复杂的数据类型如 ```string```, ```array```, ```object``` and ```resource``` 等还会使用引用计数。事实上，新的 ```zval``` 设计将引用计数从 ```zval``` 提到了 array/object 等上。这种方式会有很多好处，下面列出了部分好处：

* ```zval``` 只存简单的数值（比如布尔、整型数或浮点数），不再包涵任何内存分配。因此这节省了分配头，并且通过避免不必要的分配和释放内存改善了缓存位置，提升了性能。
* ```zval``` 存储简单的数值不需要存储引用计数和 GC 根缓冲。
* 我们避免了双重的引用计数。例如：过去，对象会既使用 ```zval``` 的引用计数，又增加了一个额外的对象引用计数，被用于支持对象传值的语法。
* 由于所有复杂的值现在集成了引用计数，它们可以独立共享 ```zval``` 的机制。尤其是现在可以共享字符串。这对 Hash 表的实现很重要，因为它不再需要复制一份非内部字符串类型的键值。

## 新的 Hash 表实现

在我们所有的准备工作之后，我们最终进入 PHP 7 中新的 Hash 表实现。让我们从 ```bucket``` 的结构开始：

```
typedef struct _Bucket {
    zend_ulong        h;
    zend_string      *key;
    zval              val;
} Bucket;
```

一个 ```bucket``` 是一个 Hash 表中的入口。它包含了很多你可以预料到的：一个 Hash ```h```，一个字符串键值 ```key```，一个 ```zval``` 值 ```val```。整型的键值会被存储在 ```h```（键值和 Hash 在这里是一样的），键值将会被设为 ```NULL```。

正如你看到的，```zval``` 直接被 ```bucket``` 所内置，所以它不需要单独的分配，我们不需要为分配付出代价。

主 Hash 表的结构更加有趣：

```
typedef struct _HashTable {
    uint32_t          nTableSize;
    uint32_t          nTableMask;
    uint32_t          nNumUsed;
    uint32_t          nNumOfElements;
    zend_long         nNextFreeElement;
    Bucket           *arData;
    uint32_t         *arHash;
    dtor_func_t       pDestructor;
    uint32_t          nInternalPointer;
    union {
        struct {
            ZEND_ENDIAN_LOHI_3(
                zend_uchar    flags,
                zend_uchar    nApplyCount,
                uint16_t      reserve)
        } v;
        uint32_t flags;
    } u;
} HashTable;
```

```bucket```（相当于数组元素）存储在 ```arData``` 数组中。这个以 2 的幂为大小进行分配，数组的大小存储在 ```nTableSize```（最小值为 8）。元素的实际数量是 ```nNumOfElements```。注意，这个数组直接包含有 ```bucket```。之前，我们使用一个指针数组去单独分配 ```bucket```，这意味着我们需要更多的分配/释放，不得不付出分配内存和额外的指针的代价。

## 元素的顺序

## Hash 表查找

## 打包后的 Hash 表

## 空 Hash 表

## 内存使用

## 性能

## 结尾思考

TODO