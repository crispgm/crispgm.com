#!/bin/bash
cd /home/runtime/src
# fetch nginx
wget http://nginx.org/download/nginx-1.10.1.tar.gz
tar zxvf nginx-1.10.1.tar.gz
# fetch modules
wget https://github.com/openresty/headers-more-nginx-module/archive/v0.30.tar.gz
tar zxvf v0.30.tar.gz
# build nginx
cd /home/runtime/src/nginx-1.10.1
./configure --prefix=/home/runtime/bin/nginx --add-module=/home/runtime/src/headers-more-nginx-module-0.30 --with-http_ssl_module --with-http_v2_module
make
make install
# check nginx
cd /home/runtime/bin/nginx
./sbin/nginx -v
# make conf
# start nginx