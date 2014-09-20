Mac OSX 提供[快捷键](http://support.apple.com/kb/HT1343?viewlocale=zh_CN)进行截图，它们分别是：

组合键                   |功能
--------------------------------------------------------------------------------
Command-Shift-3          |将屏幕捕捉到文件
Command-Shift-Control-3  |将屏幕内容捕捉到剪贴板 
Command-Shift-4          |将所选屏幕内容捕捉到一个文件，或按空格键仅捕捉一个窗口 
Command-Shift-Control-4  |将所选屏幕内容捕捉到剪贴板，或按空格键仅捕捉一个窗口

功能非常赞，用起来唯一不方便的就是文件会自动保存到桌面。
我们可以通过下面的方法，设置默认的截图保存位置。

    # /path/to/screenshot 是要设置的路径  
    defaults write com.apple.screencapture location /path/to/screenshot  
    killall SystemUIServer
