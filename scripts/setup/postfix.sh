#!/bin/bash

SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`
INSTALL_DIR=`dirname $SCRIPT_DIR`

export DEBIAN_FRONTEND=noninteractive

apt-get -q -y install postfix opendkim

mkdir /etc/mail

php $SCRIPT_DIR/install_postfix.php

