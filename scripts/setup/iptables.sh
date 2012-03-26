#!/bin/bash

# configures iptables firewall rules

SETUP_DIR=$(cd `dirname $0` && pwd)

cp $SETUP_DIR/conf/iptables.fw /root/iptables.fw
chmod 644 /root/iptables.fw
iptables-restore < /root/iptables.fw
echo "/sbin/iptables-restore < /root/iptables.fw" > "/etc/rc.local"