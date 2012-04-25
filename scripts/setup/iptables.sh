#!/bin/bash

# configures iptables firewall rules

SETUP_DIR=$(cd `dirname $0` && pwd)

cp $SETUP_DIR/conf/iptables.fw /root/iptables.fw
cp $SETUP_DIR/conf/ip6tables.fw /root/ip6tables.fw

chmod 644 /root/iptables.fw
chmod 644 /root/ip6tables.fw

iptables-restore < /root/iptables.fw
ip6tables-restore < /root/ip6tables.fw

cat <<EOF > /etc/rc.local
/sbin/iptables-restore < /root/iptables.fw
/sbin/ip6tables-restore < /root/ip6tables.fw
exit 0
EOF
