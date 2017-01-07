---
title: Home
permalink: /index.html
font_awesome: true
---
Hi, I am David Zhang, a.k.a __Crisp__ or __Crispgm__ on the internet. I work as a software engineer of web development, mainly focus on the LAMP stacks. And I am also a fullstack engineer in professional level and have sense of basic UI designs. Here is an [unofficial résumé](https://crispgm.com/resume/){:target="_blank"} for those interested.

In leisure times, photography, reading and coffee are things I greatly appreciate. I am also interested in improving productivity with great tools chain and systematic methodology. And I go to the theater cheering for my idols with _glo-sticks_ every weekends.

I advocate:

* [Free and Open Internet](https://www.google.com/intl/en/takeaction/)
* [Fresh Air in China](/page/environment-pollution-in-a-photographer-view.html)
* [Against Piracy Software](/page/piracy-software-or-app.html)
* Against Cyberbullying

Site news:

{% capture programming_posts %}
  {% for post in site.posts %}
    {% if post.type == "programming" %}
      _[{{ post.title }}]({{ post.url }})_|
    {% endif %}
  {% endfor %}
{% endcapture %}

{% capture translation_posts %}
  {% for post in site.posts %}
    {% if post.type == "translation" %}
      _[{{ post.title }}]({{ post.url }})_|
    {% endif %}
  {% endfor %}
{% endcapture %}

{% capture lifestyle_posts %}
  {% for post in site.posts %}
    {% if post.type == "lifestyle" %}
      _[{{ post.title }}]({{ post.url }})_|
    {% endif %}
  {% endfor %}
{% endcapture %}

* Checkout my latest blog post, 
    * {{ programming_posts | split: "|" | first }} of programming,
    * {{ translation_posts | split: "|" | first }} of translation,
    * {{ lifestyle_posts | split: "|" | first }} of lifestyle.
* Simple Q/A based [Wiki](/wiki/) is in beta.
* Emoji domain is available for my Instagram: [📷🌌.ws](http://📷🌌.ws )
* _Last updated on {{ site.time | date: "%Y-%m-%d" }}._
