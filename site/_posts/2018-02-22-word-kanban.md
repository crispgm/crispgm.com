---
layout: post
title: Word Kanban，看板风格的单词本
type: programming
permalink: /page/word-kanban.html
---

# 这是什么？

看板来源自日本，是丰田生产模式中的重要概念，用于流程管理[^1]。看板在生产线中主要分为两类：领取看板和生产看板。在学习单词时，我们也可以类比成：生词看板和已掌握单词看板。

在互联网产品的项目管理领域，也有很多借助看板概念的工具，比如 Trello。而我做的就是一个看板风格的背单词工具，可以看作是 Trello 上的两个 kanban，或一个分为生词和已背单词列表的 Wunderlist。

所以，这是一个单词本，由生词和已掌握单词单词两个看板组成。它还有这些 features：

* 简单易用
* 默认展示英文，悬浮会展示中文解释
* 可点击跳转到更加精准的英英词典
* 具有 API，可与 [Workflow](https://workflow.is/) 等进行整合

## tl;dr

先 show 出来：

![]({{ "/image/wk-main.png" | absolute_url }})

* 体验：<https://word-kanban.herokuapp.com/>
* GitHub：<https://github.com/crispgm/word-kanban>

# 技术方案

之前在个人项目中，我使用 Rails 作为后端，Node.js 代理加上 Preact 作为前端，并且部署在 Heroku。详细请参考 [Isomorphic React/Preact with Heroku](/page/isomorphic-react-preact-with-heroku.html)。

这次也基本如此，不过索性把 Rails 也换成 Node.js。

前后端基础部分的 Setup 工作（Webpack 配置、Express 等）完全来自其中：[rugby-board/rugby-board-node](https://github.com/rugby-board/rugby-board-node)。

## 后端

### ORM

全 Node.js 方案就需要一个数据库 ORM 层，我~~随机~~选择了 [Sequelize](http://docs.sequelizejs.com/)。

先安装包，这里我是用 Postgres。

```shell
$ yarn add sequelize
$ yarn add pg pg-hstore
```

初始化并创建 Migration：

```
$ yarn add sequelize-cli
$ cd server
$ ../node_modules/.bin/sequelize init
$ ../node_modules/.bin/sequelize model:generate --name Words --attributes text:string,userId:string,status:integer
```

运行 Migration：

```
$ ../node_modules/.bin/sequelize db:migrate
```

对于 DB 的操作就不细说了，看文档就好。

### Auth0

用户系统是个比较麻烦的东西，涉及用户信息保存、校验、权限验证和 Cookie 保存等。我没有选择自己实现一套完整的用户系统，而是选择了基于 [JSON Web Token](https://jwt.io/) 的 [Auth0](https://auth0.com/) 用户认证服务。

Auth0 是 Serverless 时代十分有用且强大的工具，无需自己建立后端就可以拥有强大的用户系统。除去基本的注册、登陆和权限校验流程，还可以一键集成第三方登陆系统，甚至提供 [Multi-factor authentication](https://en.wikipedia.org/wiki/Multi-factor_authentication) 等高级安全功能。

这是部分它支持的第三方登陆，国内的有百度、人人和微博，稍微有些匪夷所思。这里我选择 GitHub 和 Twitter。

![]({{ "/image/auth0-connections.png" | absolute_url }})

它还提供了丰富的基于实际框架的示例，我就是基于它提供的基于 [Express 的后端](https://auth0.com/docs/architecture-scenarios/application/spa-api/api-implementation-nodejs) 和 [SPA React 客户端](https://auth0.com/docs/quickstart/spa/react)的 demo 开发。

### Express

在 Express 中，创建一个中间件用于校验 JSON Web Token：

```javascript
const jwt = require('express-jwt');
const jwksRsa = require('jwks-rsa');
const cors = require('cors');

const checkJwt = jwt({
  secret: jwksRsa.expressJwtSecret({
    cache: true,
    rateLimit: true,
    jwksRequestsPerMinute: 5,
    jwksUri: 'https://crispgm.au.auth0.com/.well-known/jwks.json',
  }),
  audience: 'api.word-kanban.words',
  issuer: 'https://crispgm.au.auth0.com/',
  algorithms: ['RS256'],
});
```

在具体接口中引入中间件：

```javascript
app.post('/word/create', checkJwt, (req, res) => {
  return Word.create(req, res);
});
```

### Google Translate

前面的 features 中提到，要显示悬浮的单词的中文解释，这里引入 Google Translate API。记得把 Token 放在环境变量中。

```javascript
// token.js
const GOOGLE_TRANSLATE_API_KEY = process.env.GOOGLE_TRANSLATE_API_KEY;
```

```javascript
// translate.js
const fetch = require('node-fetch');
const { GOOGLE_TRANSLATE_API_KEY } = require('../token');

function translate(req, res) {
  const word = req.query.word;

  const input = {
    q: word,
    source: 'en',
    target: 'zh',
    format: 'text',
  };
  const url = `https://translation.googleapis.com/language/translate/v2?key=${GOOGLE_TRANSLATE_API_KEY}`;
  fetch(url, { method: 'POST', body: JSON.stringify(input) }).then(response => response.json()).then((json) => {
    res.send(json);
  });
}

module.exports = {
  translate,
};
```

### API

为了实现跟 Workflow 等自动化工具的结合，得提供一套基础简单的 API 系统。

使用私钥、用户ID和时间戳生成一个简单的 Token，存到数据库里：

```javascript
const ts = +new Date();
const token = md5(`${TOKEN_PRIVATE_KEY}${userId}${ts}`);
```

这里是[API 文档](https://github.com/crispgm/word-kanban/blob/master/docs/integration.md)。

## 前端

前端部分则继续使用 Preact 和 preact-router，跟写 React 没什么区别。用户权限校验和用户信息页完全根据 Auth0 的 React demo：
* [React Login](https://auth0.com/docs/quickstart/spa/react)
* [React User Profile](https://auth0.com/docs/quickstart/spa/react/02-user-profile)

不过有一个教训，就是没有使用状态管理（如：Redux/Mobx），导致组件间（`Kanban`, `WordList`, `WordItem`, `WordInput`）相互传递数据有些复杂。幸好只有两个列表，否则代码会不太好维护。

### React Trend

使用 Preact 的一大优势就是，在包体积小的前提下还能使用完全基于 React 的组件。

在用户设置页，我希望展示用户动态的曲线，于是找到了 [React Trend](https://github.com/unsplash/react-trend) 用于生成曲线。React Trend 是 [Unsplash](https://unsplash.com/) 开源的一个 React 组件，用在 Unsplash 的用户统计展示页（比如我的 <https://unsplash.com/@crispgm/stats>）。

![]({{ "/image/react-trend-on-unsplash.png" | absolute_url }})

这是个 React Component，我们需要用 [preact-compat](https://github.com/developit/preact-compat) 来进行兼容。兼容的方式有很多，我选择在 Webpack 进行。

```
...
  resolve: {
    extensions: ['.js', '.jsx'],
    alias: {
      'react': 'preact-compat',
      'react-dom': 'preact-compat',
    },
  },
...
```

给上数据之后，React Trend 就可以正常绘制了。

```javascript
  render() {
    return (
      <div>
        <Trend
          data={this.props.data}
          height={50}
          gradient={['#343a40', '#e64980', '#f03e3e']}
          smooth
          autoDraw
          autoDrawDuration={3000}
          autoDrawEasing="ease-out"
        />
      </div>
    );
  }
```

![]({{ "/image/word-kanban-react-trend.png" | absolute_url }})

## 完成

Auth0 跳转登陆：

![]({{ "/image/wk-login.png" | absolute_url }})

主界面：

![]({{ "/image/wk-main.png" | absolute_url }})

个人设置：

![]({{ "/image/wk-setting.png" | absolute_url }})

# 加入 Workflow

最后，我们使用 Word-kanban 的 API 创建一个可以从网页中摘录生词的 Workflow。

创建一个 Share Extension 的 Workflow，选择一个好看的图标并取一个喜欢的名字，比如我的叫：「Send My Word」。

依次加入动作：

* Choose from List
* URL
  * 添加生词的 API 接口：`https://word-kanban.herokuapp.com/api/v1/word`
* Get Contents from URL
  * 选择 `POST` 请求，加入 `token` 和 `word`，`token` 在用户页面生成粘贴过来

![]({{ "/image/send-my-word-workflow.jpg" | absolute_url }})

阅读一篇英文文章，然后选取一个不认识的单词，发送到 Word Kanban。

![]({{ "/image/send-to-word-kanban.gif" | absolute_url }})

# 最后

* 欢迎体验：<https://word-kanban.herokuapp.com/>
* 欢迎点赞：<https://github.com/crispgm/word-kanban>

<hr>

[^1]: [看板管理](https://zh.wikipedia.org/wiki/%E7%9C%8B%E6%9D%BF%E7%AE%A1%E7%90%86)
