---
layout: post
title:  48 个你需要知道的 Jekyll 使用技巧
permalink: /page/48-tips-for-jekyll-you-should-know.html
tags:
- jekyll
- tips
---
很久以前，本人的个人博客是 PHP 写的简单页面，实时转换 Markdown。后来，为了简单，用 PHP 写了一个静态博客转换器。虽用起来还比较舒服，但通用性太差了。于是转向 GitHub Pages 官方支持的 Jekyll，没想到只花了20分钟就完成了迁移，开发体验颇好。深入使用和参与了许久后，决定写一篇 Jekyll 相关的 Tips 的文章，仅供大家参考。

目前还没完全写完，先发出来。

## 简介

#### 什么是 Jekyll

[Jekyll](https://jekyllrb.com/) 是一个简单的，博客感知的，静态网站生成器。

#### 安装

首先，你得有 Ruby。对于 Mac 用户，可以使用 Homebrew 安装。

```
brew install ruby
```

然后：

```
$ gem install jekyll
```

#### Gem 安装不上或者很慢怎么办？

RubyGems 是一个优秀的包管理系统，但再优秀也扛不住墙和距离，所以一般情况下，中国大陆的用户需要更换 RubyGems 的源。

建议更换成 Ruby-China 的源，参考 <https://gems.ruby-china.org/>。

```
$ gem sources --add https://gems.ruby-china.org/ --remove https://rubygems.org/
```

同时，也广告一下本人的作品：[Gem Sources Manager](https://crispgm.github.io/gsm/)，专门用于切换源。

#### Mac OSX El Capitan 安装失败

从 Mac OSX El Capitan 开始，Apple 采取了一个叫 SIP 的东西保护系统文件夹，导致 `/usr/bin` 等文件夹无法写入。因此需要更换安装路径。

```
sudo gem install -n /usr/local/bin jekyll
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

## 配置

#### Front Matter 默认值

## Liquid 模板

#### 什么是 Liquid?

Liquid 是一个开源模版语言，由电商公司 Shopify 实现，用 Ruby 编写。Shopify 自己使用 Liquid 来构建自己电商网站模板生态。

详细文档请参考 <https://shopify.github.io/liquid/>。

#### 变量

#### if

#### for .. in

#### assign

#### capture

#### Filters

## 插件

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

#### Jemoji

你可以通过 [Jemoji](https://github.com/jekyll/jemoji) 在 Jekyll 生成的网站中，加入自己 Emoji 表情。

Emoji 语法采用 GitHub 的语法风格。

#### Jekyll Mentions

#### Jekyll Sitemap

#### Jekyll Feed

你可以通过 [Jekyll Feed](https://github.com/jekyll/jekyll-feed) 在 Jekyll 生成的网站中，生成 RSS 源。

#### Jekyll Import

[Jekyll Import](https://github.com/jekyll/jekyll-import) 支持从一些国外的主流站点导入博文，如 Blogger, WordPress 和 Tumblr 等，同样也支持 RSS 和 CSV 等数据格式导入。

#### Jekyll Archives

#### Jekyll Redirect From

[Jekyll Redirect From](https://github.com/jekyll/jekyll-redirect-from) 提供页面跳转功能，比较简单，也可以自行通过 JavaScript 实现。

## GitHub Pages

#### 创建个人主页

创建一个名为 `your-github-username.github.io` 的 Repo，`your-github-username` 是你的用户名。

只需在 Repo 中设置 GitHub Pages 的 Source，就可以开启 GitHub Pages，支持 `master`, `gh-pages`, `master` 中 `/docs` 文件夹三种 Source。

![](/image/github-pages-source.jpg)

#### 在 GitHub Pages 只可以使用部分插件

由于安全性等原因的考虑，在 GitHub Pages 平台上只能使用白名单中的 7 个 Jekyll 插件。它们分别是：Jekyll Sitemap, Jekyll SEO Tag, github-metadata, Jekyll Feed, Jekyll Redirect From, Jemoji 和 Jekyll Mentions。

详见 <https://help.github.com/articles/adding-jekyll-plugins-to-a-github-pages-site/>。

#### GitHub Pages 上的 Jekyll 只支持 kramdown

从 2016 年 5 月 1 日起，GitHub Pages 只支持 kramdown 作为 Markdown 引擎。

详见 <https://github.com/blog/2100-github-pages-now-faster-and-simpler-with-jekyll-3-0>。

#### 在根目录下创建 `.nojekyll` 文件可以跳过 Jekyll 解析

GitHub Pages 支持 Jekyll 或者原始文件。最初 GitHub Pages 只支持 Jekyll，后来 GitHub 允许在 Repo 根目录下添加 `.nojekyll` 跳过解析。详见 <https://github.com/blog/572-bypassing-jekyll-on-github-pages>。

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
