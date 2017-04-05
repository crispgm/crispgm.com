---
title: About
permalink: /about.html
---

### About

Copyright &copy; David Zhang, {{ site.time | date: "%Y" }}.

[Subscribe Blog Updates](/feed.xml){:target="_blank"}.

All the articles are written in GitHub Flavored Markdown format.

The site source is [on GitHub](https://github.com/crispgm/crispgm.com). The site theme is [working in progress](https://github.com/crispgm/jekyll-crisp-theme) but not available yet.

### License

* All the blogs are licensed under a [CC BY-NC-ND 4.0](http://creativecommons.org/licenses/by-nc-nd/4.0/) license.
* Site source is licensed under MIT License.

### Credit

<div id="credit-list">
  {% for item in site.data.credit %}
  <div class="credit-item">
    <a href="{{ item.link }}" target="_blank">
      <div class="credit-reason">
        {{ item.reason }}
      </div>
      <div class="credit-name">
        {{ item.name }}
      </div>
    </a>
  </div>
  {% endfor %}
</div>

[^7]: Disqus, <https://disqus.com/>{:target="_blank"}. Though Disqus is banned in most places of China, I will neither migrate to other comment system nor remove it. Because I like Disqus, and there isn't many comments and I am a person with anti-social tendency. You may get access to the comments if possible. Otherwise, contact me with Twitter/Weibo.
