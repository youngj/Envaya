#!/bin/bash

# configures iptables firewall rules

set -e

export DEBIAN_FRONTEND=noninteractive
export APT_LISTCHANGES_FRONTEND=none

apt-get -y install iptables-persistent

SETUP_DIR=$(cd `dirname $0` && pwd)

iptables-restore < $SETUP_DIR/conf/iptables.fw
ip6tables-restore < $SETUP_DIR/conf/ip6tables.fw
iptables-save > /etc/iptables/rules.v4
ip6tables-save > /etc/iptables/rules.v6