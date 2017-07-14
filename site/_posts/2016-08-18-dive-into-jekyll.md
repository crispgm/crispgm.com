---
layout: post
type: legacy
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

此外，主题 Gem 化带来的好处主要是主题分发的便捷性，但如果需要动手改动主题中的 css 或者 `_layouts` 文件，就比较麻烦。

所以在短期内，我还是比较推荐使用上一个稳定的版本——3.1.6 版的模式。当然，即使程序是 3.2.1 版本也可以按旧的方式来使用，因为 Jekyll 是向下兼容的。

# 快速入门

对于 Jekyll 本身和 GitHub Pages，由于市面上此类教程很多，本文就不重复造轮子了。可以阅读：

* [搭建一个免费的，无限流量的Blog----github Pages和Jekyll入门](http://www.ruanyifeng.com/blog/2012/08/blogging_with_jekyll.html), by ruanyifeng
* [Jekyll 官方文档](https://jekyllrb.com/docs/home/)

# 深入 Jekyll

Jekyll 和一般的 Ruby 程序一样，设计思想上易用性强入门简单。但 Jekyll 其实还可以有很多超频的能力。

我们可以使用 Jekyll Data Files 实现读取 Key-Value 数据库的功能，可以创建和传播自己的主题样式，并且可以结合 Liquid 模板系统创建多种类型的插件。

## Jekyll Data Files

Jekyll 虽然无法像动态网站程序一样读取数据库，但它提供了一种读取静态数据的方式，就是 Jekyll Data Files。

我们可以在 Jekyll Data Files 中使用 YAML, JSON 或 CSV 格式的文件，实现类似动态配置的效果。这些数据会被解析成变量 ```site.data.your_data``` 在 Liquid 模板中使用。

## Jekyll Themes

Jekyll 主题在支持 RubyGems 前，需要通过复制来实现。很简单粗暴，就是把已经做好的 ```_config.yml```, ```_includes```, ```_layouts``` 和相关静态文件一起复制到自己的目录中。

Jekyll 原生支持 sass，并且也可以通过插件形式使用 Less 等其它的 CSS 预处理器。

官方目前没有主题分享的网站，第三方主题资源分享网站还算比较丰富，甚至已经有了不少付费的主题。不过，这些网站都还没有迁移成 RubyGems。

3.2.0 版发布后，Jekyll 支持了 ```jekyll new-theme``` 指令，可以方便的创建自己的主题，并且发布到 RubyGems。有兴趣自制主题的 geeks，可以贡献出自己的主题。

## Liquid Templates

Jekyll 的底层模板层渲染基于开源 Ruby 库 [Liquid](https://shopify.github.io/liquid/) 实现。这是一个 [Shopify](https://www.shopify.com/) 公司开源出来的模版系统，用于 Shopify 的店铺主题系统。

Liquid 提供 Objects(对象)，Tags(标签)和 Filters(过滤器) 三种类型的模板标记。

#### Objects

__Objects__ 就是传统意义上的模板变量，用双大括号包围。Jekyll 就是将文件中解析的各种内容，通过 Liquid Objects 注入给展示层。

如下，表示页面的标题：

{% raw %}
```
{{ page.title }}
```
{% endraw %}

#### Tags

__Tags__ 主要用于提供逻辑功能和控制流，Liquid 自带的 Tags 大多都是控制流和变量相关的，如：if/foreach/assign/capture 之类的。但 Tags 实际上可以用作增加新的标签，作为逻辑功能函数的感觉。

Jekyll 就有一种扩展是通过扩展 Liquid Tags 来实现。

{% raw %}
```
{% if user %}
  Hello {{ user.name }}!
{% endif %}
```
{% endraw %}

#### Filters

__Filters__ 写法和效果都可以类比于 Shell 中的 Pipe 模式。输出的变量，通过管道符传递进行链式处理。Jekyll 官方扩展了一批 Liquid Filters 配合 Liquid Objects，便于实现博客系统。如：博客的排序、日期的格式化等。

{% raw %}
```
{{ "/my/fancy/url" | append: ".html" }}
```
{% endraw %}

注：以上例子均取自 [Liquid 官方文档](https://shopify.github.io/liquid/basics/introduction/)。

## Jekyll Plugins

Jekyll 支持多种扩展方式，分别是 Generators, Converters, Commands, Tags, Filters 和 Hooks。其中一些是 Jekyll 自身的扩展机制，而 Tags/Filters 则实质上是对 Liquid Tags/Filters 进行扩展。

扩展可以通过 Gem 或本地文件分发使用，这块的生态比较杂，主要还是通过 Jekyll 主页 [Jekyll Plugins 文档](https://jekyllrb.com/docs/plugins/)的专门区域分享插件。对于插件开发者来说，完成开发后可以发 PR 修改 Jekyll Plugins 页面把你的插件分享出来，一般都会被准许 merge（~~想骗贡献的速来~~）。

我自己也写了一个 Jekyll Plugin，名叫 jekyll-taglist。这是一个 `Liquid Tags` 扩展，其作用是收集并输出文章的标签 `page.tags` 和其计数，支持对标签的条件选择、过滤和排序。输出后，我们可以通过 CSS 实现一些展示效果。

{% raw %}
```
# 设定条件：
# - 阈值为1
# - 显示计数
# - 使用计数进行降序排序
# - 限制为30个

{% tags_list :threshold => 1, :show_count => 1, :sort_by => count, :order_by => desc, :limit => 30 %}
```
{% endraw %}

输出后的效果在我的网站[博客页底部](https://crispgm.com/blogs.html)。

有兴趣可以到 <https://github.com/crispgm/jekyll-tags-list-plugin> 详细了解。

# Jekyll 社区

由于受到 GitHub 官方的支持，Jekyll 在静态博客生成器中应该说是最热门的。这或许也是 Jekyll 的社区非常繁荣的原因。它迭代很快，维护者对 Issues 和 PRs 的处理也很快。

在工程方面，Jekyll 的自动化测试覆盖率非常高，既有基于 Cucumber 实现的 Bahavior Driven Test，也有一般的 Unit Test，通过常见的 Travis-CI 完成持续集成回归。还集成有 Code Climate 代码规范检测（通过 Rubocop 实现），代码测试覆盖率检测。目前，Jekyll 正在积极接入 AppVeyor，也就是 Windows 领域的 Travis-CI。很快即将实现多端（类 Unix 系统，如Linux/Mac 和 Windows 系统）、多平台（原生 Ruby 和 JRuby）自动化回归。

Jekyll 不仅测试做到了自动化，还做到了整体工作流的自动化。Jekyll 启用了 @jekyllbot 辅助项目维护团队：

* Merge PR：通过在 PR 中 ```@jekyllbot: merge +site``` 控制合并，并且在合并后机器人会自动根据后缀内容（+site 代表这个 PR 是对网站文档进行的修改）将变更分类加入到 changelog 中。
* 版本发布：会在 GitHub 打 tag 并发布 release，release 内容会从更新列表中拉取；自动更新并 push gemspec 文件到 RubyGems；获取这个版本内的所有变更贡献者，加入到版本发布新闻的感谢列表中。

# 启发

受到了 Jekyll 的启发，我也进行了很多关于工作流自动化方面的思考。主要做了几件小事：

#### 博客自动化部署

由于种种原因，我的博客并没有部署在 GitHub Pages 上，而是独立的主机。因此，在 push 到 GitHub 之后还需要部署到主机中。我们可以在主机上起一个代码更新部署进程，在 push 后通过 GitHub WebHooks 回调主机上的更新接口，实现自动化部署。这样，只要一 push 代码就会触发主机上的更新。

为了让 push 本身更容易，又通过 ```Rakefile``` 将 ```jekyll build``` 和 push 也自动化，完成 build 之后自动提交如：

```
git commit -m "Deployed at 2016-08-18 14:50:26 +0800"

git push origin master
```

#### 日报/周报邮件模板

日常工作中，需要每天或每周发送各类日报和周报。发送的内容一般会维护在某个表格或文档中，但邮件的收件人和抄送都是固定的一类人，标题则是标题+时间的格式，正文会有开头问好和结尾签名。这恰好是模板类需求，可以用 Liquid 实现。

对于邮件人和抄送人，建立一些 Group，并且通过 Liquid Tags 返回这些人的邮件地址。如：

{% raw %}
```
to: '{% group_members tech_core members %}'
```
{% endraw %}

对于标题，则使用 Liquid Objects 传入时间等变量，并使用 Liquid Filters 将时间转换为合适的样式。

{% raw %}
```
title: '项目日报 {{ created_time | date: "%Y%m%d" }}' 
```
{% endraw %}
