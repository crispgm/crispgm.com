---
layout: post
title: 深入 Jekyll
permalink: /page/dive-into-jekyll.html
tags:
- Jekyll
- Ruby
- GitHub
- Blog
- Markdown
- Liquid
---
# 介绍

Jekyll 是一个为博客设计的静态网站生成器，也可以用于个人、项目或组织的网站构建。可以认为，Jekyll 是一个基于文件的内容管理系统（CMS）。它使用 Ruby 编写，通过 Markdown 和 Liquid 模板生成内容。

有了 Jekyll 这类静态博客生成工具，我们不再需要使用动态语言开发和运行后端程序（如：Wordpress 和 Drupal 等），而只是需要一个静态 HTTP Server。甚至，在有了 GitHub Pages 后，连服务器资源都可以省去。当然，静态网站的好处不止是节省资源，还有安全、速度、扩展性等考虑。具体可以阅读文章：[https://www.netlify.com/blog/2016/05/18/9-reasons-your-site-should-be-static](https://www.netlify.com/blog/2016/05/18/9-reasons-your-site-should-be-static)。

美国总统奥巴马连任时的竞选 [Campaign](http://kylerush.net/blog/meet-the-obama-campaigns-250-million-fundraising-platform/) 网站就使用 Jekyll 开发。ps：不过，希拉里的技术团队又退回到了动态网站。

GitHub Pages 的背后就运行着 Jekyll，而 Jekyll 本身也是由 GitHub co-founder、前 CEO Tom Preston-Werner(@mojombo) 创立，目前由 Parker Moore(@parkr) 维护，他本人也于[2016年初加入了 GitHub](https://byparker.com/blog/2016/joining-github/)。

在 GitHub 的支持下，Jekyll 社区一直保持着极为活跃的状态，Jekyll 的 GitHub Repo 每天都处于更新的状态。近期，刚刚发布了 3.2.0 版本，开始支持基于 RubyGems 的主题。

但 3.2.0 版和作为修补的 3.2.1 版本生态还不是很完善，原有的主题大多还没有完成 Gem 化改造。官方目前没有主题分享的网站，第三方主题资源分享网站也基本都没有升级，所以反而难以用新的方式选择主题。

此外，主题 Gem 化带来的好处主要是主题分发的便捷性，但如果需要动手改动主题中的 css 或者 `_layout` 文件，就比较麻烦。

所以在短期内，我还是比较推荐使用原有的 3.1.6 版本的模式。当然，即使程序是 3.2.1 版本也可以按旧的方式来，因为 Jekyll 是向下兼容的。

# 快速入门

#### 安装

一般的标准 Linux 和 Mac 环境，都已经自带 Ruby 和 RubyGems。因此直接使用 gem 进行安装：

```
gem install jekyll
```

#### 建立一个博客

假设博客名为 myblog:

```
jekyll new myblog
```

#### 撰写内容

```
cd myblog
```

#### 调试

```
jekyll serve
```

详细的使用教程可以参考[官方文档](https://jekyllrb.com/docs/home/)。

# GitHub Pages

# 高级玩法

## Liquid Templates

Jekyll 的底层模板层渲染基于 [Liquid](https://shopify.github.io/liquid/) 实现，这是一个 [Shopify](https://www.shopify.com/) 公司开源的模版系统。

## 语法高亮

## Jekyll Data

Jekyll 虽然无法像动态网站程序一样读取数据库，但它提供了一种读取静态数据的方式，就是 Jekyll Data。

我们可以在 Jekyll Data 中使用 YAML, JSON 或 CSV 格式的文件，实现类似动态配置的效果。这些数据会被解析成变量 ```site.data.your_data``` 在 Liquid 模板中使用。

# 扩展

## Jekyll Themes

## Jekyll Plugins

Jekyll 支持多种扩展方式，分别是 Generators, Converters, Commands, Tags 和 Hooks。其中一些是 Jekyll 自身的扩展机制，而 Tags 则实质上是对 Liquid Tags 进行扩展。

扩展可以通过 Gem 或本地文件分发使用，这块的生态比较杂，主要还是通过 Jekyll 主页 Jekyll Plugins 文档的专门区域分享插件。对于插件开发者来说，完成开发后可以发 PR 修改 Jekyll Plugins 页面把你的插件分享出来，一般都会被准许 merge（~~想骗贡献的速来~~）。

# Jekyll 社区
