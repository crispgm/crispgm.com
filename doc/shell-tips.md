最近，在工作种遇到了两个有点棘手的问题。前者是不小心打错命令建立了一个"--"两个减号开头的文件，想删除删不掉；后者是，减少机器后单机文件数过多，导致定时脚本里的tar执行失败了。

#### 在shell中删除减号开头的文件

解决方法：

	rm -- "--xxxx"

#### argument list too long

由于文件数增加，脚本出现错误tar: argument list too long，也就是说tar参数太多了

解决方法：

	find /path/to/crash_log/20131229 -name '*crash*' -print > /tmp/20131229.list  
	tar zcf /path/to/client_log/20131229.tar.gz --files-from /tmp/20131229.list  
	rm /tmp/20131229.list

