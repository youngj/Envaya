#!/bin/bash

SETUP_DIR=$(cd `dirname $0` && pwd)
apt-get -y --allow-unauthenticated install php5-cli php5-curl php5-gd php5-memcache php5-mysql php-apc
cp $SETUP_DIR/conf/php-custom.ini /etc/php5/conf.d/envaya.ini
