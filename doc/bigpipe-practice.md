## 背景

随着Web页面的功能不断堆砌（其实我是极简主义者，但PM或者说是国内的风气，总是喜欢不断加入各类功能），页面需要的数据越来越多，串行连接后端的耗时自然是不断增大。常见的优化手段就是后端并行化，而对于前端来说，后端并行化只是降低了response time，但用户最终看到页面的时间并没有减少。

Facebook提出了BigPipe的方案，讲页面功能分块，分成若干个pagelet。pagelet的加载使用了http的chunked特性，采用类似Pipeline的方式进行前后端数据传输。浏览器端会首先获得一个框架层的HTML/css，以及基础js代码。同时，后端也可以进行并行化，每个pagelet完成后，通过flush输出到浏览器。浏览器端的js基于事件机制，收到数据后进行渲染。

这样，前后端就可以都做到并行化，用户可以先看到部分页面内容，从而获得了更好的用户体验。目前，国外主要是Facebook应用了这项技术，而国内微博也通过BigPipe获得了不错的效果。

![Facebook-BigPipe](http://crispgm.com/image/fb-bigpipe.jpg)

图：Facebook加载时的timing，可以看到waiting时间（也就是后端响应时间response time）明显低于content download耗时。

## 简单Demo

TODO

## 实践

### 准备工作

TODO

### 并行框架

TODO

### Buffer问题

由于“各路”buffer的存在，如果包比较小的话BigPipe的chunked输出很可能会被buffer住。针对这种情况，一般来说有两种方式。

1. 使用strpad这类函数进行填充，如：填充空格。永远将一次flush的数据填充到buffer_size。
2. 调小buffer，让数据更容易达到buffer_size。
3. 关闭buffer。

对于Nginx来说，会有proxy_buffer和fastcgi_buffer。第一种方式，不用调整buffer，但这种方式很不优雅，而且增加了带宽，并不是很合理。至于调小buffer，这看起来是一个很好的思路，然而对于gzip过的数据来说，最小的buffer可能也比较大。因此，我们选择了关闭proxy_buffer和fastcgi_buffer。

然而，这样带来了一个问题。线上运行中的Nginx 1.4.4版本过低，关闭proxy_buffer的指令proxy_buffering off原生就支持。而关闭fastcgi_buffer的fastcgi_buffering需要1.5.6版本。所以首先，我们把Nginx版本升级到了1.7.8，重编译后上线。

```
    Syntax: fastcgi_buffering on | off;
    Default:    
    fastcgi_buffering on;
    Context:    http, server, location
    This directive appeared in version 1.5.6.
```

详见[http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_buffering](http://nginx.org/en/docs/http/ngx_http_fastcgi_module.html#fastcgi_buffering)

```
    # 旧版本的Nginx并不支持fastcgi_buffering
    nginx: [emerg] unknown directive "fastcgi_buffering"
```

还有一个问题，就是我们并不想对所有请求都关闭buffer。为了将影响面做得最小，我们只想关闭特定模块的buffer。改nginx.conf固然可以实现，不过对于不支持嵌套if的nginx.conf来说这是个很不舒服的用法。

幸好，在升级的过程中，发现了一个刚好可以用http header，用于关闭buffer。

```
    Buffering can also be enabled or disabled by passing “yes” or “no” in the “X-Accel-Buffering” response header field. This capability can be disabled using the fastcgi_ignore_headers directive.
```

因此，配置上完全不用关闭buffer，只需要在php代码中加header就好，顺利把buffer优雅关闭。

```php
    header('X-Accel-Buffering: no');
```

### 效果评估

TODO