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

Hash 表背后的概念很简单：字符串类型的 key 通过一个 Hash 函数，返回一个整数。这个整数作为一个“正常”数组的下标。问题是在于两个不同的字符串可能会得到同样的 Hash 值，因为可能的字符串组合实际上是无限的，而 Hash 却受到了整型的限制。所以，这些 Hash 表需要实现某种冲突解决机制。

业界有两种主要的冲突解决方法：开地址法，如果发生冲突，元素会被存储在另外一个下标中；链地址法，所以有同样 Hash 值的元素将会被存在一个链表中。PHP 使用了后者。

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

最后 4 bytes 是 ```zval``` 结构通常未使用的（它只是明确的填充物，编译器会自动引入）。然而，在特殊的上下文中，这部分空间会被存储一些额外信息。比如：AST（抽象语法树）节点用它存储行号，虚拟机常量用它存储缓存槽的下标，Hash 表用它存储在冲突解决链表中的下一个元素 —— 最后这一部分对我们很重要。

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

```arData``` 数组按照插入的顺序存储元素。所以第一个元素会被存储在 ```arData[0]```，第二个会被存储在 ```arData[1]``` ，等等。这完全不依赖用过的 key，只跟插入顺序有关。

所以如果你有 5 个 Hash 表元素，从 ```arData[0]``` 到 ```arData[4]``` 将会被占用，下一个空闲的槽式 ```arData[5]```。我们将这个数存在 ```nNumUsed```。你可能会疑惑：为什么要分开存储？这和 ```nNumOfElements``` 有区别吗？

提出这问题是因为只看了执行插入操作时的情况。如果从 Hash 表删除一个元素时，我们显然不希望通过移动 ```arData``` 中被删除的元素后面的全部元素来使数组保持连续。作为替代，我们只是在 ```zval``` 中标注 ```IS_UNDEF``` 类型。

用下面的代码作为例子：

```
$array = [
    'foo' => 0,
    'bar' => 1,
    0     => 2,
    'xyz' => 3,
    2     => 4
];
unset($array[0]);
unset($array['xyz']);
```

这会形成如下的 ```arData``` 结构：

```
nTableSize     = 8
nNumOfElements = 3
nNumUsed       = 5

[0]: key="foo", val=int(0)
[1]: key="bar", val=int(1)
[2]: val=UNDEF
[3]: val=UNDEF
[4]: h=2, val=int(4)
[5]: NOT INITIALIZED
[6]: NOT INITIALIZED
[7]: NOT INITIALIZED
```

如你所见，前五个 ```arData``` 元素被使用了，但下标 2（key 为 0） 和 3（key 为 'xyz'） 被替换成了 ```IS_UNDEF```，因为它们被 unset 了。这些元素现在还会浪费内存。然而，一旦 ```nNumUsed``` 达到 ```nTableSize```，PHP会通过丢弃任何 ```UNDEF``` 的记录，自动压缩 ```arData``` 数组。只有当所有 ```bucket```` 真的有值才会被重分配到两倍大。

新的维护数组顺序的方式对于 PHP 5.x 的双向链表有很多优点。一个明显的优点是我们对于每个 ```bucket``` 节省两个指针，相当于 8/16 bytes。并且，这意味着数组迭代粗略如下：

```
uint32_t i;
for (i = 0; i < ht->nNumUsed; ++i) {
    Bucket *b = &ht->arData[i];
    if (Z_ISUNDEF(b->val)) continue;

    // do stuff with bucket
}
```

这相当于一个内存线性扫描，比起链表遍历（需要进行向前向后的相对随机的内存寻址）更能有效缓存。

当前实现的一个问题是 ```arData``` 从不收缩（除非明确告诉它这样做）。所以如果你创建一个几百万个元素的数组并在后来删除，数组还是会占用大量的内存。我们可能应该让 ```arData``` 的大小减半，如果使用降低到一定的程度。

## Hash 表查找

到现在，我们只是讨论了 PHP 数组如何表示顺序。实际 Hash 表的查找使用了第二个包含有 ```uint32_t``` 值的 ```arHash``` 数组。```arHash``` 数组合 ```arData``` 有相同的大小（```nTableSize```），两者实际上都是一块内存分配的。

返回的 Hash 值是由 Hash 函数（对于字符串使用 DJBX33A 算法）返回的 32 位或 64 位无符号整型，它们太大了不能直接用作 Hash 数组的下标。我们需要手心用取余操作将它们转换成表大小。我们使用 ```hash & (ht->nTableSize - 1)``` 而不是 ```hash % ht->nTableSize```，在数组大小是 2 的幂的情况下，它们结果一致但不需要“昂贵”的整数除法操作。```ht->nTableSize - 1``` 的值会被存储在 ```ht->nTableMask```。

接着，我们在 Hash 数组中寻找下标 ```idx = ht->arHash[hash & ht->nTableMask]```。这个下标相当于冲突解决链表的头，所以 ```ht->arData[idx]``` 是我们第一个检查的纪录。如果 key 和我们要查找的匹配，事情就完成了。

否则，我们必须继续查找冲突解决链表的下一个。这个元素的下标会被存储在 ```bucket->val.u2.next```，```zval``` 通常不被用到而是在特定场上下文下才有意义的 4 bytes。我们继续遍历链表指导找到正确的 ```bucket```，或者遇到 ```INVALID_IDX```，也就是并不存在查找的 key 对应的元素。

查找机制如下代码中所示：

```
zend_ulong h = zend_string_hash_val(key);
uint32_t idx = ht->arHash[h & ht->nTableMask];
while (idx != INVALID_IDX) {
    Bucket *b = &ht->arData[idx];
    if (b->h == h && zend_string_equals(b->key, key)) {
        return b;
    }
    idx = Z_NEXT(b->val); // b->val.u2.next
}
return NULL;
```

让我们考虑下对于过去的实现做了怎样的优化：在 PHP 5.x 中，冲突解决链表是一个双向链表。使用 ```uint32_t``` 下标比指针更好，因为矩阵在 64 位系统只需要一半的内存。并且，4 bytes 刚好可以将 ```next``` 节点的链接内置在没有用到的 ```zval``` 槽中，所以我们本质上是没有付出任何代价使用的。

我们现在还用了单向链表，没有 ```prev``` 节点。```prev``` 节点对于删除元素很有用，因为当你尽兴删除时，不得不调整 ```prev``` 节点的 ```next``` 节点。然而，如果按照 key 来删除，你已经在遍历冲突解决链表时知道了前一个元素。

在一些上下文删除的情况下（比如：删除迭代器当前所在的元素）可能会需要遍历冲突链表，以寻找前一个元素。但这不是一个很常见的场景，我们在别的情况下比起减少一次遍历更想节省内存。

## 打包 Hash 表

PHP 对于任何数组都使用 Hash 表。然而，对于一些很常见的连续、整数下标的数组（比如：实数组），整个 Hash 系统并没有什么用。这是 PHP 7 要引入一个“打包 Hash 表”概念的原因。

在打包 Hash 表中，```arHash``` 数组是 ```NULL``` 并且直接通过 ```arData``` 查找。如果你查找下标为 5 的元素，元素就在 ```arData[5]``` 或者根本不存在，并没有必要遍历冲突解决链表。

注意：即使是整数下标的 PHP 数组也需要维护顺序。数组 [0 => 1, 1 => 2] 和 [1 => 2, 0 => 1] 并不相同。打包 Hash 表的优化只对按照升序下标排序的数组有用。数组中可以有间隔（下标不连续），但必须是升序的。所以如果元素按照了错误的顺序插入（比如：倒序），打包 Hash 表优化将不会被使用。

除此之外还要注意，打包 Hash 表仍然会存储很多无用信息。例如，我们可以基于内存地址来确定一个 ```bucket``` 的下标，所以 ```bucket->h``` 是冗余的。```bucket->key``` 的值将会一直是 ```NULL```，所以这也浪费了内存。

我们留着这些无用的值，所以 ```bucket``` 有一样的结构，与是否使用打包无关。这意味着，迭代可以使用相同的代码。然而，我们可能会在未来切换到“完全打包”的结构，如果可能的话那时将会使用纯粹 ```zval```。

## 空 Hash 表

空 Hash 表在 PHP 5.x 和 PHP 7 中都会被特殊处理。如果你创建了空数组 ```array []```，很有可能你不会实际上插入元素。```arData/arHash``` 数组只会在你插入第一个元素时分配内存。

为了避免在很多地方对这种特殊情况做校验，一个小技巧被应用在此：当 ```nTableSize``` 被设为暗示的大小或者默认值 8，```nTableMask```（实际上是 ```nTableSize - 1```）会被设为 0。这意味着，```hash & ht->nTableMask``` 也会得到 0 的结果。

所以在这个情况中，```arHash``` 数组只有一个包涵了 ```INVALID_IDX``` 值、下标为 0的元素（这个特殊数组被称为 ```uninitialized_bucket```，并且被静态分配了内存）。当进行查找时，我们一直会找到 ```INVALID_IDX``` 值，意味着 key（实际上你只想静态分配创建一个空表）没有被找到。

## 内存使用

这应该可以覆盖 PHP 7 Hash 表实现最重要的方面。首先，我们总结下为什么新的实现省内存。我在这里只会用 64 位系统的数据，并且只看单个元素的大小，会忽略主 Hash 表的结构（这是渐进不重要的）。

在 PHP 5.x 每个元素需要巨大的 144 bytes。在 PHP 7 中，降低到了 36 bytes，或者打包情况下 32 bytes。下面这些是两者区别：

* ```zval``` 不独立分配，所以我们节省了 16 bytes。
* ```bucket``` 不独立分配，所以我们又节省了 16 bytes。
* 每个 ```zval``` 的值节省 16 bytes。
* 保证顺序不再需要 16 bytes 的双向队列，而是绝对的顺序。
* 冲突链表现在是单向的，节省了 8 bytes。此外，它现在是一个有索引的链表，并且下标被内置在 ```zval``` 中，所以我们实际上又节省了 8 bytes。

* As the zval is embedded into the bucket, we no longer need to store a pointer to it. Due to details of the previous implementation we actually save two pointers, so that’s another 16 bytes.
* The length of the key is no longer stored in the bucket, which is another 8 bytes. However, if the key is actually a string and not an integer, the length still has to be stored in the zend_string structure. The exact memory impact in this case is hard to quantify, because zend_string structures are shared, whereas previously hashtables had to copy the string if it wasn’t interned.
* The array containing the collision list heads is now index based, so saves 4 bytes per element. For packed arrays it is not necessary at all, in which case we save another 4 bytes.

However it should be clearly said that this summary is making things look better than they really are in several respects. First of all, the new hashtable implementation uses a lot more embedded (as opposed to separately allocated) structures. How can this negatively affect things?

If you look at the actually measured numbers at the start of this article, you’ll find that on 64bit PHP 7 an array with 100000 elements took 4.00 MiB of memory. In this case we’re dealing with a packed array, so we would actually expect 32 * 100000 = 3.05 MiB memory utilization. The reason behind this is that we allocate everything in powers of two. The nTableSize will be 2^17 = 131072 in this case, so we’ll allocate 32 * 131072 bytes of memory (which is 4.00 MiB).

Of course the previous hashtable implementation also used power of two allocations. However it only allocated an array with bucket pointers in this way (where each pointer is 8 bytes). Everything else was allocated on demand. So in PHP 7 we loose 32 * 31072 (0.95 MiB) in unused memory, while in PHP 5.x we only waste 8 * 31072 (0.24 MiB).

Another thing to consider is what happens if not all values stored in the array are distinct. For simplicity lets assume that all values in the array are identical. So lets replace the range in the starting example with an array_fill:

```
$startMemory = memory_get_usage();
$array = array_fill(0, 100000, 42);
echo memory_get_usage() - $startMemory, " bytes\n";
```

这个脚本的结果如下：

```
        |   32 bit |    64 bit
------------------------------
PHP 5.6 | 4.70 MiB |  9.39 MiB
------------------------------
PHP 7.0 | 3.00 MiB |  4.00 MiB
```

As you can see the memory usage on PHP 7 stays the same as in the range case. There is no reason why it would change, as all zvals are separate. On PHP 5.x on the other hand the memory usage is now significantly lower, because only one zval is used for all values. So while we’re still a good bit better off on PHP 7, the difference is smaller now.

Things become even more complicated once we consider string keys (which may or not be shared or interned) and complex values. The point being that arrays in PHP 7 will take significantly less memory than in PHP 5.x, but the numbers from the introduction are likely too optimistic in many cases.

## 性能

I’ve already talked a lot about memory usage, so lets move to the next point, namely performance. In the end, the goal of the phpng project wasn’t to improve memory usage, but to improve performance. The memory utilization improvement is only a means to an end, in that less memory results in better CPU cache utilization, resulting in better performance.

However there are of course a number of other reasons why the new implementation is faster: First of all we need less allocations. Depending on whether or not values are shared we save two allocations per element. Allocations being rather expensive operations this is quite significant.

Array iteration in particular is now more cache-friendly, because it’s now a linear memory traversal, instead of a random-access linked list traversal.

There’s probably a lot more to be said on the topic of performance, but the main interest in this article was memory usage, so I won’t go into further detail here.

## 结尾思考

PHP 7 undoubtedly has made a big step forward as far as the hashtable implementation is concerned. A lot of useless overhead is gone now.

So the question is: where we can go from here? One idea I already mentioned is to use “fully packed” hashes for the case of increasing integer keys. This would mean using a plain zval array, which is the best we can do without starting to specialize uniformly typed arrays.

There’s probably some other directions one could go as well. For example switching from collision-chaining to open addressing (e.g. using Robin Hood probing), could be better both in terms of memory usage (no collision resolution list) and performance (better cache efficiency, depending on the details of the probing algorithm). However open-addressing is relatively hard to combine with the ordering requirement, so this may not be possible to do in a reasonable way.

Another idea is to combine the h and key fields in the bucket structure. Integer keys only use h and string keys already store the hash in key as well. However this would likely have an adverse impact on performance, because fetching the hash will require an additional memory indirection.

One last thing that I wish to mention is that PHP 7 improved not only the internal representation of hashtables, but also the API used to work them. I’ve regularly had to look up how even simple operations like zend_hash_find had to be used, especially regarding how many levels of indirection are required (hint: three). In PHP 7 you just write zend_hash_find(ht, key) and get back a zval*. Generally I find that writing extensions for PHP 7 has become quite a bit more pleasant.

Hopefully I was able to provide you some insight into the internals of PHP 7 hashtables. Maybe I’ll write a followup article focusing on zvals. I’ve already touched on some of the difference in this post, but there’s a lot more to be said on the topic.
