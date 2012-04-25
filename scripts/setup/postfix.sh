#!/bin/bash

SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`
INSTALL_DIR=`dirname $SCRIPT_DIR`

apt-get -y install postfix opendkim

mkdir /etc/mail

cp $INSTALL_DIR/ssl/dkim.key  /etc/mail/dkim.key
chmod 600 /etc/mail/dkim.key

php $SCRIPT_DIR/install_postfix.php

