不说废话，正确的清除DNS缓存方式为：

    dscacheutil -flushcache
    sudo killall -HUP mDNSResponder

