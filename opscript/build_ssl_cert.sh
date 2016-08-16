#!/bin/bash
sudo apt-get install python-pip python-dev build-essential
sudo pip install --upgrade pip
sudo pip install --upgrade virtualenv

cd /home/runtime/bin

wget https://github.com/certbot/certbot/archive/v0.8.1.tar.gz

tar zxvf v0.8.1.tar.gz

cd certbot-0.8.1

./certbot-auto certonly --standalone --email crispgm@gmail.com -d crispgm.com
./certbot-auto certonly --standalone --email crispgm@gmail.com -d crisp.lol
./certbot-auto certonly --standalone --email crispgm@gmail.com -d yeyeko.gift