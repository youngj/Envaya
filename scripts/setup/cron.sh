#!/bin/bash

SETUP_DIR=$(cd `dirname $0` && pwd)

apt-get -y install daemon mcrypt

mkdir -p /var/phpcron
chmod 755 /var/phpcron

groupadd phpcron
useradd -r -d /var/phpcron -g phpcron -s /bin/false phpcron
chown phpcron.phpcron /var/phpcron

cp $SETUP_DIR/init.d/phpCron /etc/init.d/phpCron

chmod 755 /etc/init.d/phpCron
update-rc.d phpCron defaults 97
/etc/init.d/phpCron start

