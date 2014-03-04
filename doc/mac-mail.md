Mac Mail在国内网络状况下，和gmail一起总是会有点问题

收件箱里选择删除一封gmail邮件，并用command+q关掉Mail

如果删除gmail失败的话就会报错，之后Mail就会处于一种卡死的状态，退不出来也进不去

只能选择

	$ ps aux|grep Mail
	$ kill -9 xxxx

这个事情遇多了，搞了个更简单粗暴的shell脚本

	#!/bin/bash
	PID=$(ps aux|grep $1|grep -v grep|tail -1|awk '{print $2}')
	kill -9 $PID

执行一键暴力杀死Mail进程（重名被误杀后果自负）

	$ sh kill.sh Mail

