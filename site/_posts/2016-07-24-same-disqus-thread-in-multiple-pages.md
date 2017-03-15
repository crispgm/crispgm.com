---
layout: post
type: legacy
title: 不同页面载入相同 Disqus 主题
permalink: /page/same-disqus-thread-in-multiple-pages.html
tags:
- Disqus
- Tips
---
我的博客采用了开源界内普遍采用的 Disqus 提供对博客的评论支持。它用起来很简单，只需要注册一个帐户获得一个 ```Shortname``` 然后在 HTML 底部加入一段代码就可以了，Disqus 会自动抓取你的 URL 信息作为 key，在页面滑动到代码所在的区域时自动载入评论。

博客从最初到现在经历了好多个阶段：

|       | 生成器 | 域名 | 托管 | HTTPS | 评论服务 |
|-|-|-|-|-|-|
| 第一代 | PHP | 新浪二级域名 | Sina App Engine | 不支持 | 无 |
| 第二代 | PHP 自写 | GitHub 二级域名 | GitHub Pages | 不支持 | Disqus |
| 第三代 | PHP 自写 | 单个一级域名 | Linode VPS | 不支持 | Disqus |
| 第四代 | PHP 自写 | 单个一级域名 | Linode VPS | 支持 | Disqus |
| 第五代 | Jekyll | 多个一级域名 | Linode VPS | 支持 | Disqus |

其中第三、四、五代都造成了新的页面无法载入老的评论，但 Disqus 作为一个通用的第三方评论服务，提供了多种数据迁移方式帮助我轻松解决了这类问题。

### URL 变更

Disqus 对 URL 的识别也是区分协议、域名和 Path。因此，这些元素任意一个发生了变更，都会导致“新页面”没有了原有页面上的评论。

Disqus 提供了三种 URL 映射来解决这类问题。分别是：

* Domain Migration Tool：域名迁移工具
* Upload a URL map：上传 URL 映射表
* Redirect Crawler (Advanced)：重定向抓取

第二代到第三代最明确，就是有了自己的域名，URL 出现了变更，故加载不到原有的评论。因此，果断采用域名迁移工具，从原来的 http://crispgm.github.io 迁移到 http://crispgm.com 上。

第四代主要启用了 HTTPS，采用了 HTTPS 后的页面就会被视为与 HTTP 协议的页面完全不同的页面。由于域名迁移工具不支持更改协议，这就得用 URL 映射表方式。需要在本地创建一个 .csv 格式的 URL 映射表，然后上传后即可。同时由于一些页面 URL 的起初英文不太合理，所以后来优化后进行了一次变更，也采用这种方式。

### 多域名支持

第五代时，由于[多域名](/page/new-domain-name.html)的启用，访问不同域名下的同一个网页，其评论是分开的，各自是一个副本。

Disqus 官方自带的网页可视化工具已经不能支持，需要进行[程序级别的参数配置](https://help.disqus.com/customer/portal/articles/472098-javascript-configuration-variables)，需在载入代码进行配置参数。

于是，我修改了 Jekyll 的 ```_layout/post.html``` 模板中的 Disqus 片段。

{% raw %}
```
var disqus_config = function () {
  this.page.url = '{{ site.url }}{{ page.permalink }}';
  this.page.identifier = '{{ page.permalink }}';
  this.page.title = '{{ page.title }}';
};
```
{% endraw %}

现在，问题终于彻底得到了解决。
