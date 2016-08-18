#!/bin/bash
cd /home/runtime/bin/nginx && ./sbin/nginx -s quit

cd /home/runtime/bin/certbot-0.8.1

./certbot-auto certonly --standalone --email crispgm@gmail.com -d crispgm.com
./certbot-auto certonly --standalone --email crispgm@gmail.com -d crisp.lol
./certbot-auto certonly --standalone --email crispgm@gmail.com -d yeyeko.gift

cd /home/runtime/bin/nginx && ./sbin/nginx