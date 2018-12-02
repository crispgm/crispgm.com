---
layout: post
title: 我在 Slack 和自己聊天
permalink: /page/self-collaborating-with-slack.html
type: programming
tags:
- Slack
- Dev
---

![]({{ "/image/slack-logo.jpg" | absolute_url }})

[Slack](https://slack.com/) 是一款为团队协作设计的即时通信软件，维基百科的定义是：

> Slack is a cloud-based set of team collaboration tools and services.

在 Slack 中，私聊似乎没有那么重要，取而代之的是一系列不同的 Channel。这些 Channel 以 Hashtag "#" 开头，标题就是话题。如果中间讨论到跟主题相关性没那么高的内容，可以以某条聊天为基准开启一个独立的 Thread 接着聊，不用占据公频不断的引用和回复。

Slack 另一创新点是对第三方应用的集成，可以集成各类 Apps 和聊天机器人。不同类型的应用集成，和 Slack 碰撞出了更多生产力的火花，同时还催生了 ChatOps 之类的用法，大大提高了工作效率和体验。

# 为什么和自己聊天？

由于种种原因，我所在的“大公司”还在用着非常落后的即时通信软件，大环境是不能指望了。而小团队，倒是可能会有些兴趣，但要翻越知识普及、群体翻墙等诸多障碍，况且基础设施基本不支持 ChatOps，而且大部分协作还是跟外面的团队。唉，“覆巢之下，焉有完卵”啊！任你有好工具的选择，但垃圾的生态让你寸步难行。

幸好，我也有自己的 Side Project，在这里我可以决定技术栈，我可以决定协作方式。基础设施分别使用 PaaS（Heroku 运行 Rails 和 Postgres）和 VPS（Linode 里通过 Docker 跑 VPN，用 NewRelic 监控），技术栈用 Rails、React 和 Vue，代码放在 GitHub，持续集成 Travis，代码分析 CodeClimate，项目管理用 Wunderlist 和 Quip。

很好，很强大！

唯独 Slack 似乎没有找到自己的位置，毕竟自己跟自己协作没有必要聊天。但我知道 Slack 的意义可不只是当 QQ 用，用多了国外的工具链后，我发现了 Slack 存在的另一大意义——取代邮件。并且陆续找到了一些适合个人开发者的玩法，现在分享给大家。

### 通知中心

##### 服务通知

欧美IT企业对于邮件的使用程度远远超过了国内，邮件是主要通知方式，比如 Travis 默认状态下持续集成成功、失败、重新成功等得给你来一封邮件。而作为强迫症的我，就必须点进去删除一封邮件。这种方式既浪费时间又不低碳。

而 Slack 可以集成第三方 Apps，关闭邮件提醒，接管接收各类云服务的通知。于是，我在 Slack 中创建了 `#dev` 频道，收取 GitHub、Travis、CodeClimate 的通知。

比如 Travis，我们可以直接在 `.travis.yml` 指定关闭邮件提醒，并推送到 Slack：

```
notifications:
  email: false
  slack: token-of-your-slack
```

![]({{ "/image/slack-travis-msg.jpg" | absolute_url }})

不过，这里有个限制，免费用户只能添加10个应用。还好我用的云服务不足10个。真需要付费的时候，那也未尝不可，花钱买时间没什么不对的。

##### ChatOps

ChatOps 就是“聊天式运维”，通过聊天软件集成机器人方式，完成对服务器状态的查看和操作。Slack 就是这一领域的典型代表。

我会把运维相关的内容发送到 `#ops` 频道。对于 Heroku 这样成熟、开发体验友好的平台，其实没有什么需要运维的。通过 `heroku-cli` 几乎可以干任何事情，因为 `#ops` 频道只会接到代码更新部署的事件。而 Linode 那边的确需要运维，对于部署我已经通过 GitHub WebHooks 和部署脚本实现了自动化，所以这里只会接收到 NewRelic 的监控报警。

![]({{ "/image/slack-ops-msg.jpg" | absolute_url }})

不过这个地方有个尴尬😅之处，NewRelic 监控的是部署 VPN 的主机，如果报警后会发送到 Slack，但 Slack 又是通过这个 VPN 访问的，因此……

当然，我还远没有用到 ChatOps 的精髓，有空还会继续探索试一些更实用的工具，真正地 Chat 起来。

### 稍后阅读

我是稍后阅读类 App 的重度用户，Pocket 世界阅读量排名进入 Top 1%，最近也经常用 Instapaper。我也维持着一套 Reeder 订阅 RSS，然后泛读和稍后阅读的阅读 flow。

但其实很多时候，有些文章属于路上发现，到了电脑前就会立刻阅读。于是，我创建了 `#articles` 频道，把一些感兴趣且看着还不错的文章发到 Slack 中。至少，这比微信的文件同步助手手感更丝滑。

![]({{ "/image/slack-share-article.jpg" | absolute_url }})

### 看图 & 获得灵感

开发的过程是快乐的，但也时常伴随着挫折和焦急。我们需要一些方式娱乐和舒缓自己，重新获得力量和灵感。

开发间歇的娱乐活动有很多，比如游戏和音乐。而我觉得看照片比较适合 Slack，怡情养眼且不耗时，还会时不时收获灵感。

接收图片的频道有三个: `#girls` `#photography` `#design`

`#girls` 的确是美女但不开车，`#photography` 是正规摄影小作品，而 `#design` 接收设计类的图片。

为了增加分享图片的仪式感，我引入了 [Workflow](https://workflow.is/)：Post Image To Slack，通过 Action Extension 触发分享操作。

![]({{ "/image/slack-post-image-workflow.jpg" | absolute_url }})

此外，还改了改 Save from Instagram 的 Workflow，保存过后通过 Post Image To Slack 直接发给 Slack `#girls`。

##### 随机图片

休闲过后还需要来点灵感，随机是个好方法，于是剩下不多的 Apps 集成额度就用在了这里，我选择加入了 Dribbble 和 Unsplash。

通过 Dribbble，可以随机获取一张设计稿或者是通过关键词搜索一张。虽然我不是设计师，但好的设计让人愉悦，也能激发灵感。

```
/dribbble london
```

![]({{ "/image/slack-dribbble-london.jpg" | absolute_url }})

Unsplash 我在[之前的文章](/page/unsplash-simple-pure-photos.html)进行过介绍，在 Slack 中可以随机获取一张高质量摄影作品，这是一种调节开发节奏的好方法。

```
/unsplash random
```

![](https://source.unsplash.com/random)

# 最后

如果一个工具，既可以让人提高生产力，又能让人愉悦，那我们就赶快拿起<del>电话订购</del>电脑使用吧！
