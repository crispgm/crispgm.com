---
layout: post
type: programming
title: 技术周刊自动化
permalink: /page/engineering-weekly-automation.html
tags:
- Jekyll
- rake
- 周刊
- 自动化
---

## 背景

技术博客和技术周刊对于一个有技术追求的技术团队来说都是标配，意义在于团队的技术氛围。由于日常业务繁忙和技术深度等原因，大多数技术周刊都是以转载为主。技术周刊的“主编”也就是负责人，肯定需要从事文章收集、筛选、格式化、校验和编译，并不需要编辑什么内容。这部分工作量不大，但枯燥无意义。我也是其中的一员，因此我决定用“技术周刊自动化”来解决技术周刊的整套收集、筛选、校验和发布的流程。

这是我所在团队的技术博客 <https://msbu-tech.github.io/>，基于 Jekyll 创建，分为博客、周刊、分享、项目等板块。其中，最活跃的就是[技术周刊](https://msbu-tech.github.io/weekly/)。技术周刊每周都会发布，接收所有团队成员投稿，收集编译完毕后会发布到网站上，并且需要再在内部邮件组转一份。

## 如何自动化？

整体思路是基于 GitHub 构建一套自动化流程，通过 issues 进行收集，然后用脚本调用 GitHub API 处理导入和编译的各项步骤。

由于是 Jekyll 是 Ruby 编写的，因此选择 Ruby 比较合适。并且由于只是一些简单的任务式脚本，所以选择 `Rakefile` 作为载体来编写。

### 整体流程

1. 在 issues 中开启收集贴
2. 从 issues 中导入周刊（处理格式、生成 yaml 和 markdown）
3. 人工编译确认和自动化检测
4. 发布然后关闭 issue 并感谢投稿人
5. 完成 Wunderlist 中的任务

### 准备工作

#### GitHub API

选择 Ruby 还有个好处就是 GitHub 自身是用 Ruby 编写，因此 GitHub API 的 Ruby 库 [Octokit.rb](https://github.com/octokit/octokit.rb) 非常简单好用。

```ruby
client = Octokit::Client.new(:access_token => access_token)
```

#### access_token

`access_token` 不能对外暴露，因此需要在环境变量中设置或者通过指定环境变量来运行 `rake`。

#### msbu-bot

作为一个自动化的工作，我引入了一个机器人账号 [msbu-bot](https://github.com/msbu-bot/)，在调用 GitHub API 时使用。

### 文章收集

建立一个专门 Repo 收集周刊内容 [msbu-tech/weekly](https://github.com/msbu-tech/weekly)。里面没有代码，只有 `README` 来说明投稿的步骤流程，而 issues 则是收集贴。

开始收集一周的周刊时，先用创建一个收集贴。

```
$ rake open[2016-12-06]
```

也就是调用 `Octokit.rb` 发 issue 接口：

```ruby
client.create_issue(repo_name, issue_name, "MSBU Weekly #{weekly_date} is now in collecting. Post your entry following the instruction of <https://github.com/msbu-tech/weekly#投稿>.")
```

![]({{ "/image/msbu-bot-open-issue.jpg" | absolute_url }})

### 导入和编译

周刊文章收集完毕后，我们开始导入：

```
$ rake weekly[2016-12-06, true]
```

文章投稿的格式是：

```
/post
- 疯狂的JSONP
- http://www.cnblogs.com/twobin/p/3395086.html
- 何为跨域？何为JSONP？JSONP技术能实现什么？是否有必要使用JSONP技术？
- javascript, jsonp, 跨域
```

这里面的工作主要是是遍历 issue，识别 `/post` 投稿标识，然后获取标签、链接、介绍或评语、标签，而回贴人的 ID 就视为推荐人。

```ruby
issue_comment.each do |item|
  body = item[:body]
  title = ""
  link = ""
  comment = ""
  tags = Array.new
  referrer = item[:user][:login]
  body.split("\r\n").each_with_index do |line, i|
    case i
    when 0
      if !line.strip.eql?("/post")
        puts "[INFO] Skip comment #{number}:#{item[:id]}".green
        break
      end
    when 1
      title = Spacifier.spacify(line.strip.split("- ").at(1))
    when 2
      link = line.strip.split("- ").at(1)
    when 3
      comment = Spacifier.spacify(line.strip.split("- ").at(1))
    when 4
      tags = line.strip.split("- ").at(1)
      articles << { :title => title, :link => link, :comment => comment, :tags => tags, :referrer => referrer }
    end
  end
end
```

其中，`Spacifier` 是我自己做的一个 [Gem](https://github.com/crispgm/spacifier)，用于在中英文共存的字符串中，在中英文之间加一个空格。这样可以优化显示效果，之前为了一些完美主义都是手动转的。`Spacifier` 目前功能还不太完善，后续会慢慢改善。

导入文件后会提示导入文章数，并创建周刊网页和邮件所需的 Markdown 文件。

```
[INFO] Imported 8 article(s).
```

### 测试

导入了文章后，需要进行一些基本的测试。投稿人和主编们时常会写错写重复一些字段，所以做了个自动化测试进行重复检测和字段缺失检测。这也接了 Travis 进行持续集成。

同时，推荐一个好用的 Gem —— [colorize](https://github.com/fazibear/colorize)。`colorize` “打开”了 `String`，需要报错的时候，只需要在输出的字符串后面调用 `.red` 方法，就可以在控制台用红色显示，很醒目。

```ruby
puts "[ERROR] Import articles error!".red
```

### 发布

最后一步就是发布了。

文件都已经生成好，只需要 `git push` 到 GitHub，就能生效了。

我们还需要做的是告诉大家周刊发布的好消息，对投稿人表示感谢，然后关闭掉 issue。

```
$ rake publish
```

```ruby
comment = <<-EOL
  Congratulations!
  MSBU Weekly #{weekly_date} is published on <https://msbu-tech.github.io/weekly/#{weekly_date}-weekly.html>!
  Thanks #{contributors_list.join ', '} for your great contribution!
  EOL

client.add_comment(repo_name, number, comment)
client.close_issue(repo_name, number)
```

![]({{ "/image/msbu-bot-say-thanks-and-close.jpg" | absolute_url }})

### 完成 Wunderlist

这里需要再引入一个非官方的 Wunderlist API：

```ruby
gem "wunderlist-api"
```

```ruby
wl = Wunderlist::API.new({
  access_token: ENV["WLIST_ACCESS_TOKEN"],
  client_id: ENV["WLIST_CLIENT_ID"]
})

tasks = wl.tasks(["工作清单"])
tasks.each do |t|
  if t.title.eql?("MSBU Tech Weekly") && weekly_date.eql?(t.due_date)
    t.completed = true
    t.save
    show_info("Completing wunderlist task...")
    break
  end
end
```

## 写在最后

文章所说的[源码在此](https://github.com/crispgm/weekly)，如果本文帮助了你或者给了一些启发，帮忙来个 Star 吧。
