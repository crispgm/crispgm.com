#!/bin/bash
useradd runtime --home /home/runtime --password 123
mkdir -p /home/runtime
chown -R runtime /home/runtime