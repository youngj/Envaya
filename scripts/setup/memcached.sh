#!/bin/bash

apt-get -y install memcached

sed -i -e 's/127.0.0.1/0.0.0.0/' /etc/memcached.conf

/etc/init.d/memcached restart