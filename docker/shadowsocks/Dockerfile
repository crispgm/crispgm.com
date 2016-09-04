FROM ubuntu:xenial

MAINTAINER David Zhang <crispgm@gmail.com>

RUN apt-get update \
    && apt-get install python-pip \
    && pip install shadowsocks

COPY etc/shadowsocks.json /etc/shadowsocks.json

USER nobody
CMD /usr/local/bin/ssserver -c /etc/shadowsocks.json -d start
