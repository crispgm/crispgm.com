#### 什么是Fiddler

Fiddler官方给Fiddler的描述语是Web Debugging Proxy，也就是web调试代理工具。

主要功能有：

* 截取HTTP/HTTPS请求

* 修改Session

* 网络调试

* 安全测试

* 性能测试

本文，主要讨论前三项功能的使用方法，包括Fiddler的基础配置和功能以及使用内置FiddlerScript脚本进行复杂功能调试。

#### 准备和配置

Fiddler使用C#开发，支持Windows XP以上版本。对于Linux和Mac用户，有一个基于Mono的Alpha版本，经本人Mac实际测试，基本上算是不能用的状态。因此，不建议使用。

下载地址：[http://www.telerik.com/download/fiddler](http://www.telerik.com/download/fiddler)

安装完毕后，为了使用后续功能，建议安装[FiddlerScript Editor](http://www.telerik.com/download/fiddler/fiddlerscript-editor)插件，否则修改FiddlerScript会比较麻烦。

准备完毕后，请打开Fiddler进入Tools->Fiddler Options，在Connections中勾选Allow remote computers to connect，并重新启动Fiddler。

#### 网络请求抓取

完成了上述设置后，Fiddler就已经可以抓取本地的网络请求。如果需要对手机app进行调试，则需要设置手机代理。

    iPhone  
    进入Settings -> WLAN，单击你连接中的WIFI，在HTTP PROXY下选择Manual，然后Server填写Fiddler所在机器在此WIFI中的IP地址，Port是默认的8888。

    Android
    进入设置 -> WIFI，长按你连接中的WIFI，点击修改网络网络，勾选高级选项，在HTTP代理中填写主机名和端口。

    PC本地  
    用ProxySwitchy一类的插件，代理到localhost:8888

<!---
![Fiddler Capturing](http://crispgm.github.io/image/fiddler/capturing.png)
--->
选中请求后，可以查看Headers、Cookies和多种模式的(TextView/WebForms等)请求参数。

<!---
![Fiddler Request](http://crispgm.github.io/image/fiddler/request.png)
--->
如果想查看Response，则可以选中请求后点击Inspectors或者直接双击请求查看响应Headers和多种格式的响应内容。对于常见的数据结构，可以格式化展示JSON或XML。

<!---
![Fiddler Response](http://crispgm.github.io/image/fiddler/response.png)
--->

#### Web Debugging

在进行调试时，我们可以使用Fiddler的几种调试功能：

* HOSTS，等同于操作系统的HOSTS文件。

* Filters，可以修改Request或Response的包体。

* AutoResponder，可以截获请求，直接将请求代理到指定URL进行响应。

* FiddlerScript，以FiddlerScript(C#)形式，实现Filters和AutoResponder的所有功能，并且可以定制UI界面。

#### HOSTS

Fiddler自带有HOSTS功能，跟Windows自带的HOSTS应该是一回事，不过在Fiddler中操作比较方便。

而是直接从菜单进入Tools -> HOSTS...，勾选Enable remapping of requests(此处省略数十字)，就可以把请求线上服务通过HOST方式，代理到沙盒或者测试机。
<!---
![Fiddler Hosts](http://crispgm.github.io/image/fiddler/hosts.png)
--->

#### Filters

很多时候，客户端一次行为可能会产生多次HTTP请求，会造成很大的干扰，Filter功能就可以选择性截取

首先进入Filters标签，勾选Use Filters

在Request Headers中，勾选Show only if URL contains，并在后面的输入框中填写希望截取的URL。同理，也可以勾选Hide if URL contains，隐藏特定的请求。

除此之外，还可以直接修改Request或Response包体。
<!---
![Fiddler Filters](http://crispgm.github.io/image/fiddler/filters.png)
--->

#### AutoResponder

AutoReponder可以截获Request URL，并用其他URL进行Respond。利用AutoResponder，也可以实现将请求引导到指定机器的功能。

将原URL截获后，甚至可以直接修改URL。截图请求时也可以有很多方式，包括URL正则匹配、包体匹配和Headers匹配等。

此外，AutoResponder支持设置断点。可以在截获请求后，在断点处修改请求或者响应。
<!---
![Fiddler AutoResponder](http://crispgm.github.io/image/fiddler/autoresponder.png)
--->

#### FiddlerScript

FiddlerScript就是通过JScript.NET语言(可以约等于JS)，直接修改Request和Response，能够实现上述各种功能。对于熟练的专业用户来说，会更加方便灵活。

使用FidderScript建议安装第一节所说的插件，安装后就可以直接打开FiddlerScript标签，直接修改代码。

在OnBeforeRequest函数中修改：

> 添加Headers

	oSession.oRequest["http_net_type"] = "1";

> 添加Cookies

	// 增加预览机标识
	oSession.oRequest.headers.Add("Cookie", "pub_env=1");

> 匹配URL，防止添加的字段干扰到其他网页

	if (oSession.uriContains("c.tieba.baidu.com")) {  
        // TODO
	}

更多用法，请参考[Fiddler Documentation](http://docs.telerik.com/fiddler/knowledgebase/fiddlerscript/modifyrequestorresponse)

