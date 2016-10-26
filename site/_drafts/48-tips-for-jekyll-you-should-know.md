---
layout: post
title:  48 个你需要知道的 Jekyll 使用技巧
permalink: /page/48-tips-for-jekyll-you-should-know.html
tags:
- jekyll
---
## 简介

#### 什么是 Jekyll

[Jekyll](https://jekyllrb.com/) 是一个简单的，博客感知的，静态网站生成器。

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

#### 使用 `jekyll new-theme` 创建主题

## 配置

#### Frontmatter 默认值

## Liquid 模板

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

#### Jekyll SEO Tag

#### Jemoji

你可以通过 [Jemoji](https://github.com/jekyll/jemoji) 在 Jekyll 生成的网站中，加入自己 Emoji 表情。

#### Jekyll Mentions

#### Jekyll Sitemap

#### Jekyll Feed

你可以通过 [Jekyll Feed](https://github.com/jekyll/jekyll-feed) 在 Jekyll 生成的网站中，生成 RSS 源。

#### Jekyll Import

[Jekyll Import](https://github.com/jekyll/jekyll-import) 支持从一些国外的主流站点导入博文，如 Blogger, WordPress 和 Tumblr 等，同样也支持 RSS 和 CSV 等数据格式导入。

#### Jekyll Archives

#### Jekyll Redirect From

## GitHub Pages

#### 可以在 GitHub Pages 上使用 Jekyll

#### 在 GitHub Pages 只可以使用部分插件

由于安全性等原因的考虑，在 GitHub Pages 平台上只能使用白名单中的 7 个 Jekyll 插件。它们分别是：Jekyll Sitemap, Jekyll SEO Tag, github-metadata, Jekyll Feed, Jekyll Redirect From, Jemoji 和 Jekyll Mentions。

详见 <https://help.github.com/articles/adding-jekyll-plugins-to-a-github-pages-site/>。

#### GitHub Pages 上的 Jekyll 只支持 kramdown

从 2016 年 5 月 1 日起，GitHub Pages 只支持 kramdown 作为 Markdown 引擎。

详见 <https://github.com/blog/2100-github-pages-now-faster-and-simpler-with-jekyll-3-0>。

#### 在根目录下创建 `.nojekyll` 文件可以跳过 Jekyll 解析

GitHub Pages 支持 Jekyll 或者原始文件。最初 GitHub Pages 只支持 Jekyll，后来 GitHub 允许在 Repo 根目录下添加 `.nojekyll` 跳过解析。详见 <https://github.com/blog/572-bypassing-jekyll-on-github-pages>。

#### 你可以使用 CNAME 替换成自己的域名

#### 你也可以为账号下任意项目创建独立的项目页面

如项目：[crispgm/gsm](https://github.com/crispgm/gsm)，可以设置这个项目的发布源为 `master`, `gh-pages` 分支或 `master` 中的 `/docs` 目录。详见 <https://help.github.com/articles/configuring-a-publishing-source-for-github-pages/>。

设置完成后，就可以通过 <https://crispgm.github.io/gsm/> 进行访问。

## 最后

#### 本博客也是用 Jekyll 生成的

Happy Jekylling!