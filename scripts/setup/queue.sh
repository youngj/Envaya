#!/bin/bash

SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`
INSTALL_DIR=`dirname $SCRIPT_DIR`

apt-get -y install daemon

cat $SETUP_DIR/init.d/queueRunner | sed -e "s,APP_HOME=\"\",APP_HOME=\"$INSTALL_DIR\",g" > /etc/init.d/queueRunner

chmod 755 /etc/init.d/queueRunner
update-rc.d queueRunner defaults 96
/etc/init.d/queueRunner start
