#!/bin/bash
SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`
INSTALL_DIR=`dirname $SCRIPT_DIR`

apt-get -y install rabbitmq-server

rabbitmqctl delete_user guest

rabbitmq-plugins enable rabbitmq_management

mkdir /var/log/rabbit-mgmt
chown rabbitmq:rabbitmq /var/log/rabbit-mgmt

mkdir -p /etc/rabbitmq
mkdir -p /etc/rabbitmq/ssl

cat <<EOF >> /etc/sysctl.conf
net.ipv4.tcp_keepalive_time = 900
net.ipv4.tcp_keepalive_probes = 4
EOF

sysctl -p

cat <<EOF > /etc/default/rabbitmq-server
ulimit -n 8192
EOF

cat <<EOF > /etc/rabbitmq/rabbitmq.config
[
    {rabbitmq_management,  [ {http_log_dir,   "/var/log/rabbit-mgmt"} ] }
].
EOF

php $SCRIPT_DIR/install_rabbitmq.php

/etc/init.d/rabbitmq-server restart