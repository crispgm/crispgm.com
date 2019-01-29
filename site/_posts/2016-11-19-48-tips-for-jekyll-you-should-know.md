---
layout: post
type: programming
title:  48 个你需要知道的 Jekyll 使用技巧
permalink: /page/48-tips-for-jekyll-you-should-know.html
tags:
- jekyll
- tips
---
很久以前，本人的个人博客是 PHP 写的简单页面，实时转换 Markdown。后来，为了简单，用 PHP 写了一个静态博客转换器。虽用起来还比较舒服，但通用性太差了。于是转向 GitHub Pages 官方支持的 Jekyll，没想到只花了20分钟就完成了迁移，开发体验颇好。深入使用和参与了许久后，决定写一篇 Jekyll 相关的 Tips 的文章，仅供大家参考。

## 简介

#### 什么是 Jekyll

[Jekyll](https://jekyllrb.com/) 是一个简单的，博客感知的，静态网站生成器。可以认为，Jekyll 是一个基于文件的内容管理系统（CMS）。它使用 Ruby 编写，通过 Markdown 和 Liquid 模板生成内容。

Jekyll 最初由 GitHub co-founder、前首席执行官 [Tom Preston-Werner](https://github.com/mojombo) 创立。

目前，Jekyll 的维护者是 [Parker Moore](https://github.com/parkr)，他本人也于[2016年初加入了 GitHub](https://byparker.com/blog/2016/joining-github/)。

#### 安装

首先，你得有 Ruby。对于 Mac 用户，可以使用 Homebrew 安装。

```
$ brew install ruby
```

然后：

```
$ gem install jekyll
```

#### Gem 安装不上或者很慢怎么办？

RubyGems 是一个优秀的包管理系统，但再优秀也扛不住墙和距离，所以一般情况下，中国大陆的用户需要更换 RubyGems 的源。

建议更换成 Ruby-China 的源，参考 <https://gems.ruby-china.com/>。

```
$ gem sources --add https://gems.ruby-china.com/ --remove https://rubygems.org/
```

#### Mac OSX El Capitan 安装失败

从 Mac OSX El Capitan 开始，Apple 采取了一个叫 SIP 的东西保护系统文件夹，导致 `/usr/bin` 等文件夹无法写入。因此需要更换安装路径。

```
$ gem install -n /usr/local/bin jekyll
```

详见 <https://jekyllrb.com/docs/troubleshooting/#jekyll-amp-mac-os-x-1011>

#### Bundler

Jekyll 3.3.0 之后开始采用 Bundler 方式运行。安装时，需要同时安装 Bundler：

```
$ gem install jekyll bundler
```

所以所有命令可能也需要使用 Bundler：

```
$ bundle exec jekyll serve
```

## 命令

#### 使用 `jekyll new` 创建新站点

```
$ jekyll new my-jekyll-site
```

也可以：

```
$ mkdir my-jekyll-site
$ cd my-jekyll-site
$ jekyll new .
```

#### 使用 `jekyll serve` 在本地运行站点

以下三种使用方式是等价的：

```
$ jekyll serve
$ jekyll server
$ jekyll s
```

通过 `http://localhost:4000` 进行访问。

#### 使用 `jekyll build` 生成站点

以下两种使用方式是等价的：

```
$ jekyll build
$ jekyll b
```

通过 `--destination` 指定目标路径：

```
$ jekyll build --destination=/path/to/site
```

#### 使用 `jekyll new-theme` 创建主题

```
$ jekyll new-theme my-theme
```

## 基础

#### 目录结构

一个基本的 Jekyll 站点目录结构：

```
.
├── _config.yml
├── _data
|   └── members.yml
├── _drafts
|   ├── begin-with-the-crazy-ideas.md
|   └── on-simplicity-in-technology.md
├── _includes
|   ├── footer.html
|   └── header.html
├── _layouts
|   ├── default.html
|   └── post.html
├── _posts
|   ├── 2007-10-29-why-every-programmer-should-play-nethack.md
|   └── 2009-04-26-barcamp-boston-4-roundup.md
├── _sass
|   ├── _base.scss
|   └── _layout.scss
├── _site
├── .jekyll-metadata
└── index.html # 也可以是 index.md
```
* _config.yml
    * Jekyll 站点的总配置文件，有很多选项也可以通过命令行方式指定。
* _drafts
    * 未发布的草稿，文件名不需要带有日期。
* _includes
    * 代码片段，可以通过 `include` 进行引用。
* _layouts
    * 布局。布局文件可以被继承，`{% raw %}{{ content }}{% endraw %}` 用于表示被继承者的内容。
* _posts
    * 文件，命名需要以日期开头：如 `2016-12-01-my-article.md`。
* _sass
    * sass 文件，可以通过插件完成编译。也可以选择引入原生 CSS，或者 Less 等。
* _site
    * 目标文件，建议加入到 `.gitignore` 中。
* index.html/index.md
    * 首页

#### _config.yml

`_config.yml` 是整个站点的整体配置，以下是所有配置项和默认值：

```yaml
# Where things are
source:       .
destination:  ./_site
plugins_dir:  _plugins
layouts_dir:  _layouts
data_dir:     _data
includes_dir: _includes
collections:
  posts:
    output:   true

# Handling Reading
safe:         false
include:      [".htaccess"]
exclude:      ["node_modules", "vendor/bundle/", "vendor/cache/", "vendor/gems/", "vendor/ruby/"]
keep_files:   [".git", ".svn"]
encoding:     "utf-8"
markdown_ext: "markdown,mkdown,mkdn,mkd,md"

# Filtering Content
show_drafts: null
limit_posts: 0
future:      false
unpublished: false

# Plugins
whitelist: []
gems:      []

# Conversion
markdown:    kramdown
highlighter: rouge
lsi:         false
excerpt_separator: "\n\n"
incremental: false

# Serving
detach:  false
port:    4000
host:    127.0.0.1
baseurl: "" # does not include hostname
show_dir_listing: false

# Outputting
permalink:     date
paginate_path: /page:num
timezone:      null

quiet:    false
verbose:  false
defaults: []

liquid:
  error_mode: warn

# Markdown Processors
rdiscount:
  extensions: []

redcarpet:
  extensions: []

kramdown:
  auto_ids:       true
  footnote_nr:    1
  entity_output:  as_char
  toc_levels:     1..6
  smart_quotes:   lsquo,rsquo,ldquo,rdquo
  input:          GFM
  hard_wrap:      false
  footnote_nr:    1
```

#### Front Matter

Jekyll 整个站点的配置是站点根目录下的 `_config.yml` 文件，而 `_layout`, `_posts` 等目录下的文件中也可以有自己的变量。文件头部的 `yaml` 配置被称作 Front Matter。

#### Front Matter 默认值

可以使用 `defaults` 设置一个路径下 Front Matter 默认值。

```yaml
defaults:
  - scope:
      path: ""
      type: weekly
    values:
      layout: weekly
      title: 技术周刊
```

#### 忽略文件

`exclude` 用于忽略文件或文件夹，其中 `_config.yml` 和以`.`开头的文件或文件夹都会被自动忽略。后续版本，`node_modules` 等文件夹也被隐式忽略了（参考 _config.yml 章节）。

```yaml
exclude:
  - Gemfile
  - Gemfile.lock
  - README.md
  - LICENSE
```

#### 分页

Jekyll 没有内置分页功能，而是提供了一个分页插件 `jekyll-paginate`。`jekyll-paginate` 仅在特定的默认条件下生效，如果你对网站结构有自己的一套风格，`jekyll-paginate` 可能是无法满足需求的。

限制如下：

* 分页功能必须在 HTML 格式文件中调用，如：`index.html`
* 必须使用默认的链接格式 `permalink`

同时，jekyll-paginate 官方已经不再活跃开发，非官方的 [jekyll-paginate-v2](https://github.com/sverrirs/jekyll-paginate-v2) 在兼容的情况下还在活跃状态，更建议使用。

如果想继续使用，请详细阅读 <http://jekyllrb.com/docs/pagination/>。这是一个复杂的问题！

#### 文章摘要

Jekyll 提供了文章摘要摘取功能，通过 `post.excerpt` 就可以获得摘要内容。

我们也可以设置摘取摘要的分隔符：

```
excerpt_separator: <!--more-->
```

#### 评论

由于是静态站点，我们没法内建评论系统，因此需要引入一些纯前端就可以使用的评论系统。国外推荐：[disqus](https://disqus.com/)，国内推荐：[duoshuo](http://duoshuo.com/)。

#### Page

可以认为，不在 `_post` 目录下的页面都是 Page 而不是 Post，其它方面区别不大。

#### Collection

并不是每个页面都是独立“页面”和以日期为顺序的“博文”，因此 Jekyll 引入了 Collection。Collection 可以根据路径定义一类具有相同属性的页面集合。Collection 也可以通过 Front Matter 设定默认值。

#### Data

Data 相当于动态页面中的数据库，Jekyll Data 支持 `yaml`, `json`, `CSV` 三种格式，可以通过 `site.data` 直接访问。

例如：

团队成员有 Fa, Li, Zhang 三人，于是我们在默认路径 `_data` 创建一个数据文件 `member.yml`：

```yaml
- name: Fa
- name: Li
- name: Zhang
```

在页面中显示团队成员列表：

```
{% raw %}{% for member in site.data.member %}
<ul>
  <li>{{ member.name }}</li>
</ul>
{% endfor %}{% endraw %}
```

## Liquid 模板

#### 什么是 Liquid?

Liquid 是一个开源模版语言，由电商公司 Shopify 实现，用 Ruby 编写。Shopify 自己使用 Liquid 来构建自己电商网站模板生态。

详细文档请参考 <https://shopify.github.io/liquid/>。

Jekyll 使用 Liquid 作为模版引擎，构建页面。

#### 变量

```
{% raw %}<title>
{{ page.title }}
</title>{% endraw %}
```

其中，Jekyll 预设了 `site`, `layout`, `page`, `content` 四个全局变量。

#### 逻辑判断

Liquid 的逻辑判断跟 Ruby 完全一致。

* 常见语言中的 `if`, `else if`, `else` 在 Liquid 中的对应是 `if`, `elsif`, `else`。同时，Liquid 也可以使用 Ruby 特有的 `unless`。
* 常见语言中的 `switch`, `case` 在 Liquid 中的对应是 `case`, `when`。

为了简单，只以 `if` 为例：

```
{% raw %}{% if page.syntax_highlight != 'false' %}
<link rel="stylesheet" href="{{ site.assets }}/css/zenburn.css">
{% endif %}{% endraw %}
```

#### 遍历

在 Liquid 中可以通过 `for` `in` 语法遍历数组，并且支持一般语言循环中的 `continue` 和 `break`。

除此之外，还可以使用 `offset` 和 `limit` 控制遍历范围，通过 `reversed` 进行倒序。

```
{% raw %}{% for post in site.posts reversed %}
<a href="{{ post.permalink }}">{{ post.title }}</a>
{% endfor %}{% endraw %}
```

详见 <https://shopify.github.io/liquid/tags/iteration/>

#### 赋值

使用 `assign` 进行赋值：

```
{% raw %}{% assign my_variable = false %}{% endraw %}
```

使用 `capture` 进行捕捉赋值：

```
{% raw %}{% capture my_variable %}
I am being captured.
{% endcapture %}{% endraw %}
```

#### Liquid Filters

Liquid Filters 是一种针对 Liquid 中变量的过滤器，语法是：

```
{% raw %}{{ var | filter: "param" }}{% endraw %}
```

除去 Liquid 自身丰富的过滤器之外，Jekyll 还额外扩展了一些实用的：

* `cgi_escape` `url_escape` `xml_escape`
    * 对变量进行相应的 escape
* `markdownify` `scssify` `sassify` `jsonify`
    * 对变量内容的格式转换
* `where` `where_exp` `group_by` `sort`
    * 对变量数据的查询排序等操作

详见 <http://jekyllrb.com/docs/templates/#filters>

## 插件

#### 插件简介

Jekyll 支持使用插件进行扩展，插件的类型分为：Generators、Converters、Commands、Hooks、Liquid Tag、Liquid Filter 等。

如果希望开发插件，请参考 <http://jekyllrb.com/docs/plugins/>

#### 使用插件

1. 基于 Gem 的方式

    对于已经发布到 RubyGems 的插件，推荐使用这种方式。只需要在 `_config.yml` 中 `gems` 字段加入相应插件名称即可。Jekyll 3.5.0 版之后请使用 `plugins` 字段配置。

2. 基于本地文件

    对于没有发布的插件，可以在 `_plugins` 文件夹中直接引入 `*.rb` Ruby 源文件。

## 常用插件

#### Jekyll Watch

Jekyll “必备”的插件，因为这是 Jekyll 程序的依赖，只是因为程序结构设计被剥离成了插件。它在本地预览时，提供文件变更的自动更新，让我们每次刷新都能自动看到最新的内容。

Jekyll Watch 是自动开启的：

```
$ jekyll serve --watch
```

#### Jekyll Compose

安装了 [Jekyll Compose](https://github.com/jekyll/jekyll-compose) 后，Jekyll 会额外提供一些命令，便于发布管理博文。

创建草稿：

```
$ jekyll draft
```

创建新博客：

```
$ jekyll post
```

创建新博客：

```
$ jekyll page
```

发布草稿：

```
$ jekyll publish
```

撤销发布：

```
$ jekyll unpublish
```

#### Jekyll Admin

[Jekyll Admin](https://github.com/jekyll/jekyll-admin) 是一个 CMS 风格的图形化后台管理插件，可以在本地给用户提供服务。

![](https://github.com/jekyll/jekyll-admin/raw/master/screenshot.png)

#### Jekyll SEO Tag

[Jekyll SEO Tag](https://github.com/jekyll/jekyll-seo-tag) 帮你生成一大堆 Meta 标签。

#### Jemoji

你可以通过 [Jemoji](https://github.com/jekyll/jemoji) 在 Jekyll 生成的网站中，加入自己 Emoji 表情。

Emoji 语法采用 GitHub 的语法风格。

#### Jekyll Mentions

[Jekyll Mentions](https://github.com/jekyll/jekyll-mentions) 允许你在文章中直接“@” GitHub 或其它网站用户。

#### Jekyll Feed

你可以通过 [Jekyll Feed](https://github.com/jekyll/jekyll-feed) 在 Jekyll 生成的网站中，生成 RSS 源。

#### Jekyll Import

[Jekyll Import](https://github.com/jekyll/jekyll-import) 支持从一些国外的主流站点导入博文，如 Blogger, WordPress 和 Tumblr 等，同样也支持 RSS 和 CSV 等数据格式导入。

#### Jekyll Archives

[Jekyll Archives](https://github.com/jekyll/jekyll-archives) 用于生成带标签和分类的『存档』页面。

#### Jekyll Redirect From

[Jekyll Redirect From](https://github.com/jekyll/jekyll-redirect-from) 提供页面跳转功能，比较简单，也可以自行通过 JavaScript 实现。

## GitHub Pages

#### 创建个人主页

创建一个名为 `your-github-username.github.io` 的 Repo，`your-github-username` 是你的用户名。

只需在 Repo 中设置 GitHub Pages 的 Source，就可以开启 GitHub Pages，支持 `master`, `gh-pages`, `master` 中 `/docs` 文件夹三种 Source。

![]({{ "/image/github-pages-source.jpg" | absolute_url }})

#### 在 GitHub Pages 只可以使用部分插件

由于安全性等原因的考虑，在 GitHub Pages 平台上只能使用白名单中的 7 个 Jekyll 插件。它们分别是：Jekyll Sitemap, Jekyll SEO Tag, github-metadata, Jekyll Feed, Jekyll Redirect From, Jemoji 和 Jekyll Mentions。

详见 <https://help.github.com/articles/adding-jekyll-plugins-to-a-github-pages-site/>。

#### GitHub Pages 上的 Jekyll 只支持 kramdown

从 2016 年 5 月 1 日起，GitHub Pages 只支持 kramdown 作为 Markdown 引擎。

详见 <https://github.com/blog/2100-github-pages-now-faster-and-simpler-with-jekyll-3-0>。

#### 在根目录下创建 `.nojekyll` 文件可以跳过 Jekyll 解析

GitHub Pages 支持 Jekyll 或者原始文件。最初 GitHub Pages 只支持 Jekyll，后来 GitHub 允许在 Repo 根目录下添加 `.nojekyll` 跳过解析。

详见 <https://github.com/blog/572-bypassing-jekyll-on-github-pages>。

#### 你可以使用自己的域名

在 Source 的根路径下，创建 `CNAME` 写入域名，然后把 DNS 解析到 GitHub Pages 的 IP：`192.30.252.153`和`192.30.252.154`。

详见 <https://help.github.com/articles/using-a-custom-domain-with-github-pages/>

#### 你也可以为账号下任意项目创建独立的项目页面

任何一个项目的 Repo，都可以开启这个项目的 GitHub Pages。开启方式同个人主页。

如项目：[crispgm/gsm](https://github.com/crispgm/gsm)，设置完成后，就可以通过 <https://crispgm.github.io/gsm/> 进行访问。

#### 你可以通过 `site.github` 获得 Repo 的信息

由上面所说的 [github-metadata](https://github.com/jekyll/github-metadata) 提供服务，通过 GitHub API 获取 Repo 的信息。当然，在本地环境下，也可以通过手动安装 github-metadata 来使用。

#### 私有 Repo 也可以开启 GitHub Pages

GitHub 的付费用户建立的私有项目 Private Repo，也可以开启 GitHub Pages。

#### GitHub Pages 无法在使用 `gh-pages` 分支作为源的情况下关闭

有些奇怪的设定，真想关闭直接删除掉，在 Source 选择 None 就好。

## 最后

#### 本博客也是用 Jekyll 生成的

Happy Jekylling!
