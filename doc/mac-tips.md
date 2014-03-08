> mac下ssh避免多次输入密码

编辑文件

    sudo vim /etc/ssh_config

加入

    Host *   
    ControlMaster auto  
    ControlPath ~/.ssh/master-%r@%h:%p  

保存后，重新启动终端即可生效

> mac终端显示颜色

修改~/.bash_profile，添加

	export CLICOLOR=1
	export LSCOLORS=gxfxaxdxcxegedabagacad

