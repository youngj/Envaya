#!/bin/bash
SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`
INSTALL_DIR=`dirname $SCRIPT_DIR`

apt-get -y install rabbitmq-server

rabbitmqctl delete_user guest

php $SCRIPT_DIR/install_rabbitmq.php
