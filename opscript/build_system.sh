#!/bin/bash
useradd runtime --home /home/runtime --password 123
mkdir -p /home/runtime
mkdir -p /home/runtime/bin
mkdir -p /home/runtime/src
chown -R runtime /home/runtime

echo "export  LC_ALL=en_US.UTF-8"  >> ~/.bash_profile