在安装有OSX 10.9 Mavericks的Mac中，包括升级或者直接购买的Mac，在合上盖子或者休眠重开后，经常会遇到没有声音的情况  
忍了很久后，今天终于找到[解决方法](http://www.v2ex.com/t/95465)

解决方法：

    sudo kextunload /System/Library/Extensions/AppleHDA.kext  
    sudo kextload /System/Library/Extensions/AppleHDA.kext

OSX 升级到 10.10 Yosemite，此问题没有再出现过，应该是苹果彻底修复了。
