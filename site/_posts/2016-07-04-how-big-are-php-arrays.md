---
layout: post
type: translation
title: 『翻译』PHP 数组有多大？
date: 2016/07/05 23:45:00 +0800
permalink: /page/how-big-are-php-arrays.html
tags:
- PHP
- PHP5
- Internal
- Array
- Hash
---

提示：很大！

# 声明

本文翻译自 PHP 开发组成员 Nikita Popov([@nikic](https://github.com/nikic)) 的博客，文章根据中文习惯做了一定的调整。

原文：[How big are PHP arrays (and values) really? (Hint: BIG!)](https://nikic.github.io/2011/12/12/How-big-are-PHP-arrays-really-Hint-BIG.html)

# Disclaimer

This article is translated from a blog post of Nikita Popov([@nikic](https://github.com/nikic)), in which I have done a small amount of adjustment based on the reading habit of Chinese.

Original Page: [How big are PHP arrays (and values) really? (Hint: BIG!)](https://nikic.github.io/2011/12/12/How-big-are-PHP-arrays-really-Hint-BIG.html)

# 正文

__更新__(2016-06-14)：这篇文章主要关于 PHP 5 的内存使用。PHP 7 的内存占用，对于本文提到的情况，大约得到了3倍的优化。请阅读 [hashtable implementation in PHP 7](https://nikic.github.io/2014/12/22/PHPs-new-hashtable-implementation.html) 获得更多信息。

在一开始，我想感谢[约翰尼斯 Johannes](http://schlueters.de/blog/) 和 [泰瑞尔 Tyrael](http://www.tyrael.hu/)，他们帮助我寻找到了更多的隐藏内存使用。

这篇博客，我将使用如下脚本作为样本研究 PHP 数组（及其值）的总体内存占用情况，这个脚本会创建 100000 个唯一的整型数组元素并计算其内存占用：

```
$startMemory = memory_get_usage();
$array = range(1, 100000);
echo memory_get_usage() - $startMemory, ' bytes';
```

你估计它的内存占用会有多大呢？简单的说，一个整型数是 8 bytes（在一个 64 位 UNIX 机器上使用 ```long``` 类型），且有 100000 个整型数。所以显而易见，你需要 800000 bytes。这大概是 0.76 MB。

现在，我们尝试运行上面的测试代码，结果需要 14649024 bytes。是的，你没听错，是 13.97 MB —— 是估计值的 18 倍。

所以，额外多出的 18 倍来自于哪？


## 概述

对于那些不想看完整个故事的人，这里给出了一个简单的涉及到的不同组件的内存占用情况：

```
                             |  64 bit   | 32 bit
---------------------------------------------------
zval                         |  24 bytes | 16 bytes
+ cyclic GC info             |   8 bytes |  4 bytes
+ allocation header          |  16 bytes |  8 bytes
===================================================
zval (value) total           |  48 bytes | 28 bytes
===================================================
bucket                       |  72 bytes | 36 bytes
+ allocation header          |  16 bytes |  8 bytes
+ pointer                    |   8 bytes |  4 bytes
===================================================
bucket (array element) total |  96 bytes | 48 bytes
===================================================
total total                  | 144 bytes | 76 bytes
```

上面的数字根据你的操作系统、编译器以及编译器参数的不同会有所不同。比如：如果你使用 debug 模式编译 PHP 或者开启线程安全，你将会得到不同的数值。但我认为上述值的大小代表了生产版本 64 位 Linux 下 PHP 5.3 的一般情况。

如果你用 144 bytes 乘以 100000 个元素，会得到 14400000 bytes，也就是 13.73 MB。这很接近实际值，剩下的大多是给未初始化的 ```Bucket``` 的指针，我将会在后续提到它。

现在，如果你想获得对上述数值更细节的分析，请继续阅读 :)

## zvalue_value union

首先看一下 PHP 是如何存储数值的。众所周知 PHP 是一个弱类型语言，所以它需要在不同类型中快速切换。因此 PHP 使用 ```union```（[联合](http://en.wikipedia.org/wiki/Union_%28computer_science%29)） 实现数值存储，定义在 zend.h 的 307行：

```
typedef union _zvalue_value {
    long lval;                // For integers and booleans
    double dval;              // For floats (doubles)
    struct {                  // For strings
        char *val;            //     consisting of the string itself
        int len;              //     and its length
    } str;
    HashTable *ht;            // For arrays (hash tables)
    zend_object_value obj;    // For objects
} zvalue_value;
```

如果你不懂 C 语言，那也不是一个问题，因为这段代码很直接：```union``` 是一种可以将数值以不同类型存取的方式。比如说，如果你使用 ```zvalue_value->lval```，你将会获得以整型解析的值。如果你使用 ```zvalue_value->ht```，值会被解析成一个指向 Hashtable（哈希表）的指针（也就是 php 所谓的数组）。

但是，我们不需要过于关注这些。最重要的是，一个 ```union``` 的大小等于它最大的元素的大小。这个 ```union``` 中，最大的组成部分是 ```string``` 结构（```zend_object_value``` 结构的大小同 ```string``` 一样，为了简单只说后者）。```string``` 包含一个指针（8 bytes）和一个整型（4 bytes），总共 12 bytes。由于内存对齐的原因（12 bytes 不够 cool，因为它不是 64 bits / 8 bytes 的倍数），整个结构的总大小位 16 bytes，因此这是这个 ```union``` 的整体大小。

因此现在我们知道，由于 PHP 动态类型的原因，每个值不是需要 8 bytes，而是 16 bytes。乘以 100000 后得到 1600000 bytes，即 1.53 MB。但是，实际值是 13.97 MB，所以我们还是没有得到答案。

## zval 结构

这很符合逻辑：```union``` 只是存值本身，而 PHP 显然还需要存储它的类型以及一些垃圾回收信息。你可能已经听说过，带有这些信息的结构体叫做 ```zval```。想获取更多信息，我推荐阅读[萨拉·戈尔蒙（Sara Golemon）的一篇文章](http://blog.golemon.com/2007/01/youre-being-lied-to.html)。无论如何，[结构体定义](http://lxr.php.net/xref/PHP_5_4/Zend/zend.h#318)如下：

```
struct _zval_struct {
    zvalue_value value;     // The value
    zend_uint refcount__gc; // The number of references to this value (for GC)
    zend_uchar type;        // The type
    zend_uchar is_ref__gc;  // Whether this value is a reference (&)
};
```

一个结构体的大小由它的元素总和决定：```zvalue_value``` 是 16 bytes，```zend_uint``` 是 4 bytes，```zend_uchars``` 每个 1 byte。总共 22 bytes。还是因为内存对齐，实际的大小是 24 bytes。

如果你存储 100000 个 24 bytes 的元素，总共需要 2400000 bytes，也就是 2.29 MB。差值在缩小，但真实值仍旧有 6 倍大。

## 垃圾周期回收器（对于 PHP 5.3）

PHP 5.3 引入了一种新的[解决循环引用的垃圾回收器](http://php.net/manual/en/features.gc.collecting-cycles.php)，PHP 还需要存更多的数据来做这件事。我不想在这里介绍这个算法是如何运转的，你可以阅读上面的链接。对于我们的内存大小计算来说最重要的内容是，PHP 会把每个 ```zval``` 包入一个 ```zval_gc_info``` 结构。

```
typedef struct _zval_gc_info {
    zval z;
    union {
        gc_root_buffer       *buffered;
        struct _zval_gc_info *next;
    } u;
} zval_gc_info;
```

你可以看到，Zend 只是加入了一个包涵有两个指针的 ```union```。正如你记得的，一个 ```union``` 的大小等于它的最大元素的大小：两个元素都是指针，因此都是 8 bytes。所以这个 ```union``` 也是 8 bytes。

如果我们加到前面计算的 24 bytes 上就得到了 32 bytes，乘以 100000 结果是 3.05 MB。

## Zend MM allocator

C 不像 PHP，它不会帮你管理内存，你需要自己关注内存的分配情况。PHP 使用了一个专门为这个需求进行过专门优化的自定义内存管理器：[Zend Memory Manager](http://php.net/manual/en/internals2.memory.php)（简称 Zend MM）。Zend MM 基于 [Doug Lea's malloc](http://g.oswego.edu/dl/html/malloc.html) 并且增加了一些 PHP 特定的优化和功能（如内存限制，请求后的内存清理等）。

对于我们的计算最重要的是，这个“MM”在进行了每次分配后添加一个分配头。[定义如下](http://lxr.php.net/xref/PHP_5_4/Zend/zend_alloc.c#336)：

```
typedef struct _zend_mm_block {
    zend_mm_block_info info;
#if ZEND_DEBUG
    unsigned int magic;
# ifdef ZTS
    THREAD_T thread_id;
# endif
    zend_mm_debug_info debug;
#elif ZEND_MM_HEAP_PROTECTION
    zend_mm_debug_info debug;
#endif
} zend_mm_block;

typedef struct _zend_mm_block_info {
#if ZEND_MM_COOKIES
    size_t _cookie;
#endif
    size_t _size; // size of the allocation
    size_t _prev; // previous block (not sure what exactly this is)
} zend_mm_block_info;
```

可以看出，结构的定义中混入了很多与编译参数相关的定义。这些编译参数每被设定一个，分配头将会更大。当你的编译 PHP 时，启用堆保护、多线程、debug 和 MM cookies 时，将会达到最大值。

对于这个例子，我们假设这些编译参数都被关闭了。所剩下的只有两个 ```size_t``` 类型的 ```_size``` 和 ```_prev```。一个 ```size_t``` 是 8 bytes（64 位情况下），所以每次内存分配所增加的内存分配头总共有 16 bytes。

现在所我们又要再次调整 ```zval``` 的大小。实际上，由于有分配头，它不是 32 bytes，而是 48 bytes。乘以 100000 个元素后是 4.58 MB。真实的大小是 13.97 MB，我们已经大约达到了三分之一。

## Buckets

迄今为止，我们只考虑了单独的值。但 PHP 的数组结构占用了很多空间：“数组”实际上在这里是个错误的命名。PHP 数组其实是 HashTable（哈希表）和 Dictionary（词典）。所以，哈希表是如何工作的？简单的说，每当 Hash 生成一个 key，Hash 会用一个偏移量将指向到真实的 C 数组。因为 Hash 会冲突，有相同 Hash 值的所有元素会被储存到一个链表中。当存取元素时，PHP 首先计算一个元素的 Hash 值，寻找正确的 ```Bucket``` 然后遍历链表，逐个元素对比实际的 key。```Bucket``` 的定义如下（[见 zend_hash.h#54](http://lxr.php.net/opengrok/xref/PHP_5_4/Zend/zend_hash.h#54)）：


```
typedef struct bucket {
    ulong h;                  // The hash (or for int keys the key)
    uint nKeyLength;          // The length of the key (for string keys)
    void *pData;              // The actual data
    void *pDataPtr;           // ??? What's this ???
    struct bucket *pListNext; // PHP arrays are ordered. This gives the next element in that order
    struct bucket *pListLast; // and this gives the previous element
    struct bucket *pNext;     // The next element in this (doubly) linked list
    struct bucket *pLast;     // The previous element in this (doubly) linked list
    const char *arKey;        // The key (for string keys)
} Bucket;
```

如你所见，PHP 需要存储一大堆数据来实现抽象的数组数据结构（PHP 数组同时是数组、词典和链表，这自然需要很多信息）。每个单独元素的大小分别是 ```unsigned long``` 8 bytes，```unsigned int``` 4 bytes 以及 7 个 8 bytes 的指针。这总共是 68 bytes， 内存对齐后需要 72 bytes。

```Bucket``` 同 ```zval``` 一样需要分配内存头，所以我们需要 16 bytes 给内存头，因此总共 88 bytes。同时，我们需要存储指向 ```Bucket``` 中“真实” C 数组的指针(```Bucket **arBuckets;```)。跟在上文中提到的一样，每个元素需要额外增加 8 bytes。所以总共每个 ```Bucket``` 需要 96 bytes 的存储空间。

所以如果我们需要给每个值一个 ```Bucket```，那需要 96 bytes 给 ```Bucket``` 和 48 bytes 给 ```zval```，总共 144 bytes。对于 100000 个元素，就是 14400000 bytes，也就是 13.73 MB。

_神秘被解决了。_

## 等等，还有 0.24 MB！

那剩下的 0.24 MB 是由于存在没有初始化的 ```Bucket```：真实的 C 数组大小理想状态下应该是接近需要存储的数组元素数量的。这样的话 Hash 冲突较少（除非你希望浪费更多内存）。但显然 PHP 不会在每次增加数组元素时都重新分配整个数组 —— 这会非常非常非常慢。每当 ```Bucket``` 满了，PHP 会将 ```Bucket``` 扩大一倍。因此，数组的大小永远是 2 的幂。

在我们的例子中是 2^17 = 131072。由于我们只用了 100000 个元素，所以会有 31072 剩余。这些 ```Bucket``` 不会被分配（所以我们不需要使用完整的 96 bytes），但 ```Bucket``` 指针（```Bucket``` 数组内部存储的）的内存还是需要分配。所以我们需要使用 8 bytes（一个指针）* 31072 个元素。这是 248576 bytes 或 0.23 MB，这与失踪的内存是符合的。（当然，还有一小部分字节失踪了，但我并不想在这全部 cover 到。那些是类似于 HashTable 的结构体本身、变量等。）

_神秘真正被解决了。_

## 这表明了什么？

__PHP 不是 C__。这是它主要体现给我们的。你不能指望一个像 PHP 一样很动态的语言与 C 语言一样有同样高效的内存使用。你就是不能。

但如果你想节省内存，可以考虑使用 ```SplFixedArray``` 存储大型的、静态的数组。

看一下修改后的脚本：

```
$startMemory = memory_get_usage();
$array = new SplFixedArray(100000);
for ($i = 0; $i < 100000; ++$i) {
    $array[$i] = $i;
}
echo memory_get_usage() - $startMemory, ' bytes';
```

它做了相同的事情，但如果你运行它，会发现它“只”会消耗 5600640 bytes。也就是每个元素 56 bytes，远比每个元素 144 bytes 的普通数组少。这是因为一个固定数组不需要 ```Bucket``` 结构：所以它每个元素只需要一个 ```zval```（48 bytes）和一个指针（8 bytes），也就是我们观察到的 56 bytes。
