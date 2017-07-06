---
layout: post
title: Isomorphic React/Preact with Heroku
permalink: /page/isomorphic-react-preact-with-heroku.html
type: programming
tags:
- Preact
- React
- Rails
- Heroku
---

[Rugby News Board](http://rugbynews.space) is the side project mentioned in title, which is a simple news site for Rugby sport in Chinese. I made it because there is not Rugby news site in Chinese and I love Rugby.

Rugby News Board was [firstly built](https://github.com/crispgm/rugby-board) with Rails, with simple news channels. By Rails, I could be able to make prototype within 2 days. I wrote a `news` table with ORM with SQLite. It ran on Linode VPS and I did DevOps by myself.

# Heroku

Since I read about the article from Unsplash [_Scaling Unsplash with a small team_](https://medium.com/unsplash-unfiltered/scaling-unsplash-with-a-small-team-fbdd55571906), I realized that I should emigrate from VPS to PaaS to avoid unnecessary cost for infrastructures. I have kept on [evaluating containers and other technology](/page/containerize-my-vps.html) on the VPS but it was neither profitable nor productive to the project.

As a result, I started to migrate to [Heroku](https://heroku.com/), as I sought for more efficiency, reliability and convenience. The process didn't take too much effort. The only major change to my Rails app is that the database was [changed into Postgres](https://devcenter.heroku.com/articles/sqlite3).

# Isomorphic JavaScript

Rails is flexible both backend and frontend, with components support for everything, and it is a perfect MVC framework for multiple pages application. However, MVVM
is the future, e.g. React. It is a bit more reasonable, elegant and expressive way to make data reactive than modify DOM with jQuery. **Moreover, I just want to keep on track with modern frontend development**.

By the way, I feel quite strange using CoffeeScript, as part of Rails stacks. Being surrounded by Vanilla JavaScript, EcmaScript of all versions, and TypeScript, I have no interest in learning an unpopular JavaScript replacement, which some dudes said it was dead.

I tried using React with [react-rails](https://github.com/reactjs/react-rails) to build translator system of Rugby names. It is merely based on Rails, and we can use React Component with `react_component` tag and simply pass the data to the component:

```html
<div class="section-item">
  <%= react_component "RugbyDictQuery", {title: "Rugby Dictionary"} %>
</div>
```

react-rails is a great binding between Rails and React. But the trend is isomorphic React application as frontend and backends serve only APIs. I read some articles and slides from companies use both Rails and React, such as [_The Evolution of Airbnb's Frontend_](https://www.slideshare.net/spikebrehm/the-evolution-of-airbnbs-frontend/) and [_Scaling Unsplash with a small team_](https://medium.com/unsplash-unfiltered/scaling-unsplash-with-a-small-team-fbdd55571906) again.

They both prefered build isomorphic JavaScript application with Node and React. I followed Unsplash, because we use the same PaaS and frameworks.

## React App

Initially, I setup my project with [_JavaScript Stack from Scratch_](https://github.com/verekia/js-stack-from-scratch). I won't post many [codes](https://github.com/rugby-board/rugby-board-node) here, since it is routine work with React.

## Build after Deployment

Deployment on Heroku is easy, but I had to commit `dist` folder to codebase at first, which was not an elegant way. Then I found `heroku-postbuild` hook, by which deployment can trigger Webpack build.

Add `heroku-postbuild` to `package.json`：

```
"heroku-postbuild": "webpack -p --config ./config/webpack.prod.config.js"
```

# Optimizing and Preact

After the launch of new Node/React application, I could hardly ignore the long gap before I saw the page finally. Heroku is commonly lag in China due to firewall. I put it down to this reason until I found the `bundle.js` is 1.8 mb.

The reason is Webpack production configuration is wrong, I fixed it by adding `UglifyJsPlugin` and it is now 700 kb. 

```js
plugins: [
  HtmlWebpackPluginConfig,
  new webpack.NoEmitOnErrorsPlugin(),
  new webpack.DefinePlugin({ 'process.env.NODE_ENV': '"production"' }),
  new webpack.optimize.OccurrenceOrderPlugin(),
  new webpack.optimize.UglifyJsPlugin({
    compress: {
      screw_ie8: true,
      warnings: false,
    },
    mangle: {
      screw_ie8: true,
    },
    output: {
      comments: false,
      screw_ie8: true,
    },
  }),
],
```

Even 700 kb is not good. And then I came up with an idea of using [Preact](https://github.com/developit/preact), "Fast 3kb React alternative with the same ES6 API", which is confirmed by my frontend expert friend [@yanni4night](https://github.com/yanni4night).

I had no many codes so I use Preact directly, without using preact-compat to make it compatible with React.

```
import { h, Component } from 'preact';
```

It goes as soon as I change the import statements on the top.

react-router was replaced by preact-router, which is even user-friendly. The parameters are simply passed to `props`:

Router:

```
<EventPage path="/event/:name" />
```

EventPage Component:

```
export default class EventPage extends Component {
  constructor(props) {
    super(props);

    this.state = {
      eventName: props.name,
      pageNum: props.page || 0,
    };
  }
}
```

After the optimization, it is *only* 364 kb and typically takes less than 2 seconds to load the application. Using Preact is a good choice, while we can get the advantage of React and benefit from the lightweight.

# Conclusion

The development experience of frontend is great, with fancy frameworks, tools and developer's ecology. It evolves very fast, faster than evolvement of its documentation. The examples of the documentations fail frequently as version increases fast. But, it's fantastic that they often give accurate migration advice so that I can fix immediately. For other problems, StackOverflow provides strong supports as frontend people are the most vigorous in the industry.
