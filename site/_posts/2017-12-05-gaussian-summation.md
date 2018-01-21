---
layout: post
title: 高斯速算法和微小的优化
type: programming
permalink: /page/gaussian-summation.html
---

今天在阅读 Linus Torvalds 的自传《Just for Fun》时（或者说是重温，因为大学时粗略的看过一遍），Linus 在书中提到了1加到100的高斯速算法。作为擅长中小学数学的中国人应该都很清楚这个是什么，我就不在此介绍了。

在书中他主要表达的是对故事真实性的一些质疑，因为他认为：

> 伟大的数学家不会采用既繁琐又无趣的方法解决问题，因为他们能理解问题背后的真正内涵，并且利用这个内涵找到更为简便的方法，从而得到答案。

容易走神的我，于是就在想：对于用笔算的小学生们，Bruteforce 意味着从1加到100，在很细心不出现低级失误的前提下，我想花个20分钟还是必要的。而对于掌握了高斯速算法的人来说，顶多10秒钟。这之间差了千倍。

那对于计算能力已经经历了几十年摩尔定律发展的情况下，高斯速算法和“无脑”的 Bruteforce 循环之间能差到多少？

```ruby
require 'benchmark/ips'

def gaussian_add_to_100
  (1 + 100) * 100 / 2
end

def bruteforce_add_to_100
  total = 0
  1.upto(100) do |i|
    total += i
  end
  total
end

Benchmark.ips do |x|
  x.report('gaussian add to 100') do
    gaussian_add_to_100
  end

  x.report('brute force add to 100') do
    bruteforce_add_to_100
  end

  x.compare!
end
```

好奇的我，写了一段简单的 Ruby benchmark 程序。结果是（为了显示好看与实际输出有格式调整）：

```
Warming up:
  gaussian add to 100
    157.824k i/100ms
  brute force add to 100
    11.804k i/100ms

Calculating:
  gaussian add to 100
    5.103M (±12.3%) i/s
    25.094M in   5.003086s
  brute force add to 100
    140.711k (± 7.9%) i/s
    708.240k in   5.065663s

Comparison:
  gaussian add to 100:
    5102771.3 i/s
  brute force add to 100:
    140711.1 i/s - 36.26x  slower
```

相差了足足36倍。

产品开发中，我们往往会抱怨，程序“简单”到不用让你把链表拼来接去、不需要让你折腾二叉树。但我们真正把事情做好了吗？这些小小的优化并非微不足道。
