#!/bin/bash

SETUP_DIR=$(cd `dirname $0` && pwd)
apt-get -y install php5-cli php5-curl php5-gd php5-memcache php5-mysql php5-mcrypt

mkdir -p /etc/php5/conf.d
cp $SETUP_DIR/conf/php-custom.ini /etc/php5/conf.d/php-custom.ini

ln -fs /etc/php5/conf.d/php-custom.ini /etc/php5/cli/conf.d/php-custom.ini