---
layout: post
type: translation
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

Original Post: [PHP's new hashtable implementation](https://nikic.github.io/2014/12/22/PHPs-new-hashtable-implementation.html)

# 正文

大约三年前，我写过一篇[分析 PHP 5 数组内存消耗的文章](/page/how-big-are-php-arrays.html)。作为即将推出的 PHP 7 的工作的一部分，Zend 引擎很大一部分已经被重写，这些工作针对于更小的数据结构和更少的内存分配。在这篇文章中，我将对新的 Hash 表实现做一个概述，并展示为什么它比之前的实现更有效。

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

换句话说，PHP 7 中的数组在 32 位系统中节省了 2.5 倍的内存，在 64 位（LP64）系统中节省了 3.5 倍，这相当的可观。

## Hash 表介绍

本质上，PHP 的数组其实是有序词典。也就是说，它们代表了一个由 key/value 对组成的有序列表，其中 key/value 映射由 Hash 表实现。

[Hash 表](http://en.wikipedia.org/wiki/Hash_table)是一个普遍的数据结构。从根本上，它解决了计算机只可以直接使用连续整数下标表达数组，而程序员常希望使用字符串或者其它更复杂的类型作为 key 的问题。

Hash 表背后的概念很简单：字符串类型的 key 通过一个 Hash 函数，返回一个整数，这个整数作为一个“正常”数组的下标。问题是在于两个不同的字符串可能会得到同样的 Hash 值，因为字符串组合的可能性实际上是无限的，而 Hash 却受到了整型的限制。所以，这些 Hash 表需要实现某种冲突处理机制。

业界有两种主要的冲突处理方法：开地址法，如果发生冲突，元素会被存储在另外一个下标中；链地址法，所以有同样 Hash 值的元素将会被存在一个链表中。PHP 使用了后者。

通常来说，Hash 表不会有明确的顺序：元素存储在数组底层的顺序依赖于 Hash 函数，并且相当的随机。但这个行为和 PHP 数组的语义不一致：如果你遍历一个 PHP 数组，你将会得到按插入顺序排列的元素。这意味着，PHP 的 Hash 表实现不得不加入额外的机制来记录数组元素的顺序。

## 旧的 Hash 表实现

我在这里只会简短的概述一下旧的 Hash 表实现，想获得更全面的解释可以阅读 PHP 内核书的 [Hash 表章节](http://www.phpinternalsbook.com/hashtables/basic_structure.html)。下图所示是一个 PHP 5 Hash 表的高级视图：

![](https://nikic.github.io/images/basic_hashtable.svg)

“冲突处理”链表中的元素是 ```bucket```。每个 ```bucket``` 会被单独分配。图片隐藏了 ```bucket``` 中实际存储的值（只显示了 key）。值被存储在独立分配的 ```zval``` 结构中，结构有 16 bytes（32 位）或 24 bytes（64 位）。

另一件事就是图片没有体现出冲突处理链表实际是一个双向链表（这样可以便于删除操作）。在冲突处理链表旁边，还有另一个双向链表用于存储数组元素的顺序。对于一个有以```"a", "b", "c"``` 3 个 key 为顺序的数组，顺序链表如下：

![](https://nikic.github.io/images/ordered_hashtable.svg)

所以，为什么旧的 Hash 表结构无论是内存消耗还是性能上都如此的低效？这有很多主要的原因：

* ```bucket``` 需要独立分配内存。内存分配慢而且需要额外增加 8 / 16 bytes，独立分配内存也意味着 ```bucket``` 在内存中会更分散，降低缓存效果。
* ```zval``` 也需要独立分配内存。同样的，内存分配慢而且会导致更多内存分配。除此之外，这还需要在每个 ```bucket``` 中存储一个指向 ```zval``` 的指针。因为旧的实现过度通用，它实际需要不止一个，而是两个指针。
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

你可以安全的忽略在定义中的宏 ```ZEND_ENDIAN_LOHI_4```，它只用于在不同字节顺序的机器下保证可预计的内存布局。

```zval``` 结构有三部分：第一个成员是值。```zend_value``` 联合体占用 8 bytes 用于存储不同类型的值，包括整型、字符串、数组等。具体存储什么依赖于 ```zval``` 的类型。

第二部分是 4 bytes 的 ```type_info```，包含有实际的类型（像 ```IS_STRING``` 或 ```IS_ARRAY```），并且有一些额外的提供这个类型信息的标识。比如，如果这个 ```zval``` 存储了一个对象，则类型的标识会表明它是一个非常量、引用计数了的、可垃圾收集的、非复制类型。

最后 4 bytes 是 ```zval``` 结构中通常未使用的（它只是明确的填充物，编译器会自动引入）。然而，在特殊的上下文中，这部分空间会被存储一些额外信息。比如：AST（抽象语法树）节点用它存储行号，虚拟机常量用它存储缓存槽的下标，Hash 表用它存储在冲突处理链表中的下一个元素 —— 最后这一部分对我们很重要。

如果你同之前的 ```zval``` 实现对比，一个最特别的不同是：新的 ```zval``` 结构不再有引用计数 ```refcount```。这后面的原因是，```zval``` 不再被单独分配，而是被直接集成在任何存储它的地方（比如：Hash 表中的 ```bucket```）。

```zval``` 本身不再使用引用计数，而复杂的数据类型如 ```string```, ```array```, ```object``` 和 ```resource``` 等还会使用引用计数。事实上，新的 ```zval``` 设计将引用计数从 ```zval``` 提到了 ```array``` 和 ```object``` 等结构上。这种方式会有很多好处，下面列出了部分好处：

* ```zval``` 只存简单的数值（比如布尔、整型数或浮点数），不再包涵任何内存分配。因此这节省了分配头的额外消耗，并且通过避免不必要的分配和释放内存改善了缓存访问，提升了性能。
* ```zval``` 存储简单的数值不需要存储引用计数和 GC 根缓冲。
* 我们避免了双重的引用计数。例如：过去，对象会既使用 ```zval``` 的引用计数，又增加了一个额外的对象引用计数，被用于支持对象传值的语法。
* 由于所有复杂的值现在集成了引用计数，它们可以独立共享 ```zval``` 的机制，尤其是现在可以共享字符串。这对 Hash 表的实现很重要，因为它不再需要复制一份非留存（译者注：此处 interned 没有找到好的翻译，[string interning](https://en.wikipedia.org/wiki/String_interning) 是指一种为每个不同的不可变字符串值只存储一个拷贝的方法）的字符串类型的键值。

## 新的 Hash 表实现

在我们所有的准备工作之后，我们最终进入 PHP 7 中新的 Hash 表实现。让我们从 ```bucket``` 的结构开始：

```
typedef struct _Bucket {
    zend_ulong        h;
    zend_string      *key;
    zval              val;
} Bucket;
```

一个 ```bucket``` 是一个 Hash 表中的入口。它包含了很多你可以预料到的：一个 Hash 值 ```h```，一个字符串键值 ```key```，一个 ```zval``` 值 ```val```。整型的键值会被存储在 ```h```（键值和 Hash 在这里是一样的），```key``` 将会被设为 ```NULL```。

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

```bucket```（相当于数组元素）存储在 ```arData``` 数组中，以 2 的幂为大小进行分配，数组的大小存储在 ```nTableSize```（最小值为 8）。元素的实际数量是 ```nNumOfElements```。注意，这个数组直接包含有 ```bucket```。之前，我们使用一个指针数组去单独分配 ```bucket```，这意味着我们需要更多的分配/释放，不得不付出分配内存和额外的指针的代价。

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

如你所见，前五个 ```arData``` 元素被使用了，但下标 2（key 为 0） 和 3（key 为 'xyz'） 被替换成了 ```IS_UNDEF```，因为它们被 unset 了。这些元素现在还会浪费内存。然而，一旦 ```nNumUsed``` 达到 ```nTableSize```，PHP会通过丢弃任何 ```UNDEF``` 的记录，自动压缩 ```arData``` 数组。只有当所有 ```bucket``` 真的有值才会被重分配到两倍大。

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

到现在，我们只是讨论了 PHP 数组如何表示顺序。实际 Hash 表的查找使用了第二个包含有 ```uint32_t``` 值的 ```arHash``` 数组。```arHash``` 数组合 ```arData``` 有相同的大小（```nTableSize```），两者实际上都被分配在同一内存分片上。

返回的 Hash 值是由 Hash 函数（对于字符串使用 DJBX33A 算法）返回的 32 位或 64 位无符号整型，它们太大了不能直接用作 Hash 数组的下标。我们需要首先用取余操作将它们转换成表的大小。我们使用 ```hash & (ht->nTableSize - 1)``` 而不是 ```hash % ht->nTableSize```，在数组大小是 2 的幂的情况下，它们结果一致但不需要“昂贵”的整数除法操作。```ht->nTableSize - 1``` 的值会被存储在 ```ht->nTableMask```。

接着，我们在 Hash 数组中寻找下标 ```idx = ht->arHash[hash & ht->nTableMask]```。这个下标相当于冲突处理链表的头部，所以 ```ht->arData[idx]``` 是我们第一个检查的纪录。如果 key 和我们要查找的匹配，事情就完成了。

否则，我们必须继续查找冲突处理链表的下一个。这个元素的下标会被存储在 ```bucket->val.u2.next```，这个 ```zval``` 通常不被用到而是在特定上下文下才有意义的 4 bytes。我们继续遍历链表直到找到正确的 ```bucket```，或者遇到 ```INVALID_IDX```，也就是并不存在查找的 key 对应的元素。

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

让我们考虑下对于过去的实现做了怎样的优化：在 PHP 5.x 中，冲突处理链表是一个双向链表。使用 ```uint32_t``` 下标比指针更好，因为在 64 位系统只需要一半的内存。并且，4 bytes 刚好可以将 ```next``` 节点的链接内置在没有用到的 ```zval``` 槽中，所以我们本质上是没有付出任何代价使用的。

我们现在还用了单向链表，没有 ```prev``` 节点。```prev``` 节点对于删除元素很有用，因为当你进行删除时，不得不调整 ```prev``` 节点的 ```next``` 节点。然而，如果按照 key 来删除，你已经在遍历冲突处理链表时知道了前一个元素。

在一些上下文情况下的删除（比如：删除迭代器当前所在的元素）可能会需要遍历冲突链表，以寻找前一个元素。但这不是一个很常见的场景，对于这种情况我们比起减少一次遍历更倾向于节省内存。

## 打包 Hash 表

PHP 对于任何数组都使用 Hash 表。然而，对于一些很常见的连续、整数下标的数组（比如：实数组），整个 Hash 系统并没有什么用。这是 PHP 7 要引入一个“打包 Hash 表”概念的原因。

在打包 Hash 表中，```arHash``` 数组是 ```NULL``` 并且直接通过 ```arData``` 查找。如果你查找下标为 5 的元素，元素就在 ```arData[5]``` 或者根本不存在，并没有必要遍历冲突处理链表。

注意：即使是整数下标的 PHP 数组也需要维护顺序。数组 [0 => 1, 1 => 2] 和 [1 => 2, 0 => 1] 并不相同。打包 Hash 表的优化只对按照升序下标排序的数组有用。数组中可以有间隔（下标不连续），但必须是升序的。所以如果元素按照了错误的顺序插入（比如：倒序），打包 Hash 表优化将不会被使用。

除此之外还要注意，打包 Hash 表仍然会存储很多无用信息。例如，我们可以基于内存地址来确定一个 ```bucket``` 的下标，所以 ```bucket->h``` 是冗余的。```bucket->key``` 的值将会一直是 ```NULL```，所以这也浪费了内存。

我们留着这些无用的值，所以 ```bucket``` 有一样的结构，与是否使用打包无关。这意味着，迭代可以使用相同的代码。然而，我们可能会在未来切换到“完全打包”的结构，如果可以那时将会使用纯粹的 ```zval``` 数组。

## 空 Hash 表

空 Hash 表在 PHP 5.x 和 PHP 7 中都会被特殊处理。如果你创建了空数组 ```array []```，很有可能你不会实际上插入任何元素。```arData/arHash``` 数组只会在你插入第一个元素时分配内存。

为了避免在很多地方对这种特殊情况做校验，在此应用了一个小技巧：当 ```nTableSize``` 被设为暗示的大小或者默认值 8 时，```nTableMask```（实际上是 ```nTableSize - 1```）会被设为 0。这意味着，```hash & ht->nTableMask``` 也会得到 0 的结果。

所以在这个情况中，```arHash``` 数组只有一个带有 ```INVALID_IDX``` 值、下标为 0 的元素（这个特殊数组被称为 ```uninitialized_bucket```，并且被静态分配了内存）。当进行查找时，我们会一直找到 ```INVALID_IDX``` 值，意味着 key（实际上你只想静态分配创建一个空表）没有被找到。

## 内存使用

内存使用应该涉及了 PHP 7 Hash 表实现最重要的方面。首先，我们总结下为什么新的实现省内存。我在这里只会用 64 位系统的数据，并且只看单个元素的大小，会忽略主 ```HashTable``` 结构（这是渐进不重要的）。

在 PHP 5.x 每个元素需要巨大的 144 bytes。在 PHP 7 中，降低到了 36 bytes，或者打包情况下 32 bytes。下面这些是两者区别：

* ```zval``` 不独立分配，所以我们节省了 16 bytes。
* ```bucket``` 不独立分配，所以我们又节省了 16 bytes。
* 每个 ```zval``` 的值节省 16 bytes。
* 保证顺序不再需要 16 bytes 的双向队列，而是绝对的顺序。
* 冲突链表现在是单向的，节省了 8 bytes。此外，它现在是一个有下标的链表，并且下标被内置在 ```zval``` 中，所以我们实际上又节省了 8 bytes。
* 由于 ```zval``` 被内置在 ```bucket``` 中，我们不再需要为它存一个指针。由于之前实现的细节，我们实际上节省了两个指针，所以又省了 16 bytes。
* key 的长度不再存储在 ```bucket``` 中，这有 8 bytes。然而，如果 key 是字符串而不是整型的话，key 的长度还是会被存到 ```zend_string``` 中。这种情况下确切的内存影响很难去量化，因为 ```zend_string``` 是共享的，鉴于之前如果字符串没被留存，Hash 表就不得不复制字符串。
* 数组包含的冲突链表现在是基于下标的，所以每个元素节省 4 bytes。对于打包数组，这完全没有必要，我们还可以再省 4 bytes。

然而，需要明确的是这个总结让事情看起来比它们多方面的实际影响看着更好。首先，新的 Hash 表实现使用了很多内置（与分配相对应的）结构。这对事情有什么负面影响呢？

如果你确实注意看了本文开始时的测量后的数据，你会发现 64 位 PHP 7 中的一个 100000 个元素的数组占用 4 MB 内存。在这个事例中，我们用了打包数组来做，所以我们实际上预计会占用 32 * 100000 = 3.05 MB 内存。这后面的原因是，我们给任何东西都分配 2 的幂大小的内存。在这里，```nTableSize``` 的大小是 2^17 = 131072，所以我们需要分配 32 * 131072 bytes 内存（也就是 4 MB）。

当然，之前的 Hash 表实现也会按照 2 的幂值来分配内存。然而，它只对有 ```bucket``` 指针的数组按这种方式分配（这里每个指针是 8 bytes），其它任何东西都按需分配。所以，在 PHP 7 中我们在浪费了 32 * 31072 (0.95 MB) 无用内存，而在 PHP 5.x 中我们只浪费 8 * 31072 (0.24 MB)。

另一件需要考虑的事情是，如果不是所有存储的值都不相同的情况下会发生什么。为了简单，我们假设所有的值是完全相同的。所以让我们把开头的脚本 ```range``` 函数替换成 ```array_fill```。

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

你可以从内存使用中看到，PHP 7 保持了同样的内存占用。这没有理由发生变化，因为每个 ```zval``` 都是分开的。在 PHP 5.x 中内存消耗现在明显降低了，因为只需要一个 ```zval``` 来存储所有值。所以，虽然 PHP 7 还有一些优势，但差距变小了。

事情会变得更加复杂，如果我们考虑字符串作 key（这可能是共享的或者是留存的）和复杂的 value。这种情况下 PHP 7 会显著比 PHP 5.x 节省内存，但介绍里的数字在很多情况下可能过于乐观。

## 性能

我们已经谈论了很多关于内存占用的问题，现在我们进入下一个环节，叫做性能。最终，phpng 项目的目标并不是改善内存占用，而是提升性能。内存占用优化只是达成目标的一个方法，因为减少内存占用可以得到更好的 CPU 缓存利用，以达到更好的性能。

然而，这当然有一些其他的让新实现更快的原因：首先，我们减少了内存分配。我们对于每个元素减少了两次分配，依赖于值是否是共享的。内存分配是一个消耗很大的操作，所以这个效果相当有意义的。

数组迭代现在特别的对缓存友好，由于现在是线性内存遍历，而不是随机存取的链表遍历。

也许还需要在性能主题多说一些，但本文的主要兴趣在内存使用上，所以我就不在这扩展细节了。

## 结尾思考

PHP 7 的 Hash 表实现毫无疑问迈进一大步，很多无用的内存不再使用了。

所以问题是：我们何去何从？一个想法是，我前面也提到的，在升序整数 key 的情况下使用完全打包的 Hash。这意味着使用纯 ```zval``` 数组，这是我们可以不开始特殊处理一致类型的数组之前的最好的方法。

我们也有一些其它的方向。比如，从链地址法改为开地址法（比如：使用 Robin Hood 查找法），可以既在内存使用上（没有冲突处理链表）又在性能上（更好的缓存效率，依赖于查找算法的细节）获得优化。然而，开地址法相对难以和排序需求结合，所以这可能在实际情况下并不可行。


另一个想法是把 ```bucket``` 中的 ```h``` 和 ```key``` 字段整合在 ```bucket``` 结构中。整数 key 只使用 ```h```，字符串 key 也会在 key 中保存 hash 值。然而，这么做可能对性能造成不利影响，因为提取 hash 值也需要额外的内存开销。

我需要说的最后一件事是，PHP 7 不止是优化了 Hash 表的内部实现，还改进了相关的 API。我通常甚至要查看一些简单的操作如 ```zend_hash_find``` 怎么用，尤其是需要考虑到需要多少层间接的调用（提示：3 层）。在 PHP 7中，你只需要写 ```zend_hash_find(ht, key)``` 然后得到 ```*zval```。总的来说，为 PHP 7 写扩展变得更有意思了。

希望我能够给你们一些 PHP 7 Hash 表内核的洞见。可能我还会写一篇关于 ```zval``` 的后续文章，我已经在本文中触及到了它的一些不同，但对于这个话题还可以说更多的东西。
