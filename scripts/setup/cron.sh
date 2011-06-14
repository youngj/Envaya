#!/bin/bash

SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`
INSTALL_DIR=`dirname $SCRIPT_DIR`

apt-get -y install daemon

cat $SETUP_DIR/init.d/phpCron | sed -e "s,APP_HOME=\"\",APP_HOME=\"$INSTALL_DIR\",g" > /etc/init.d/phpCron

chmod 755 /etc/init.d/phpCron
update-rc.d phpCron defaults 97
/etc/init.d/phpCron start

