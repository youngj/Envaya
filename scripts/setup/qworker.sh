#!/bin/bash

SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`
INSTALL_DIR=`dirname $SCRIPT_DIR`

apt-get -y install daemon

mkdir -p /var/qworker
chmod 755 /var/qworker

groupadd qworker
useradd -r -d /var/qworker -g qworker -s /bin/false qworker
chown qworker.qworker /var/qworker

mkdir -p /var/log/qworkers
chmod 755 /var/log/qworkers
chown qworker.qworker /var/log/qworkers

cat $SETUP_DIR/init.d/qworkers | sed -e "s,APP_HOME=\"\",APP_HOME=\"$INSTALL_DIR\",g" > /etc/init.d/qworkers

chmod 755 /etc/init.d/qworkers
update-rc.d qworkers defaults 96
/etc/init.d/qworkers start
