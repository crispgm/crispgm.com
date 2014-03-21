### 什么是Fiddler

Fiddler官方给Fiddler的描述语是Web Debugging Proxy，也就是web调试代理工具。

主要功能有：

* 截取HTTP/HTTPS请求

* 修改Session

* 网络调试

* 安全测试

* 性能测试

本文，主要讨论前三项功能的使用方法，包括Fiddler的基础配置和功能以及使用内置FiddlerScript脚本进行复杂功能调试。

### 准备和配置

Fiddler使用C#开发，支持Windows XP以上版本。对于Linux和Mac用户，有一个基于Mono的Alpha版本，经本人Mac实际测试，基本上算是不能用的状态。因此，不建议使用。

下载地址：[http://www.telerik.com/download/fiddler](http://www.telerik.com/download/fiddler)

安装完毕后，为了使用后续功能，建议安装[FiddlerScript Editor](http://www.telerik.com/download/fiddler/fiddlerscript-editor)插件，否则修改FiddlerScript会比较麻烦。

准备完毕后，请打开Fiddler进入Tools->Fiddler Options，在Connections中勾选Allow remote computers to connect，并重新启动Fiddler。

### 网络请求抓取

完成了上述设置后，Fiddler就已经可以抓取本地的网络请求

![Fiddler Filters](http://crispgm.github.io/image/capturing.png)

并且可以通过代理功能，将其他在同一WIFI中手机或电脑代理到你的IP上，这时就可以获取它们的网络请求。

    iPhone  
    进入Settings -> WLAN，单击你连接中的WIFI，在HTTP PROXY下选择Manual，然后Server填写Fiddler所在机器在此WIFI中的IP地址，Port是默认的8888。

    Android
    进入设置 -> WIFI，长按你连接中的WIFI，点击修改网络网络，勾选高级选项，在HTTP代理中填写主机名和端口。



选中请求后，可以查看Headers、Cookies和多种模式的(TextView/WebForms等)请求参数。

![Fiddler Filters](http://crispgm.github.io/image/request.png)

如果想查看Response，则可以选中请求后点击Inspectors或者直接双击请求查看响应Headers和多种格式的响应内容。对于常见的数据结构，可以格式化展示JSON或XML。

![Fiddler Filters](http://crispgm.github.io/image/response.png)

### Filter

很多时候，客户端一次行为可能会产生多次HTTP请求，会造成很大的干扰，Filter功能就可以选择性截取

首先进入Filters标签，勾选Use Filters

在Request Headers中，勾选Show only if URL contains，并在后面的输入框中填写希望截取的URL。同理，也可以勾选Hide if URL contains，隐藏特定的请求。

![Fiddler Filters](http://crispgm.github.io/image/filters.png)

### HOSTS

Fiddler自带有HOSTS功能，跟Windows自带的HOSTS应该是一回事，不过在Fiddler中操作比较方便，不用进入C:\Windows\System32\Drivers\Etc\HOSTS。

而是直接从菜单进入Tools -> HOSTS...，勾选Enable remapping of requests(此处省略数十字)

![Fiddler Hosts](http://crispgm.github.io/image/hosts.png)