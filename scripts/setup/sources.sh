#!/bin/bash

# overwrite sources.list with our own sources

if ! grep -q sources.sh /etc/apt/sources.list ; then 

cat <<EOF > /etc/apt/sources.list.d/envaya.list 

deb http://www.rabbitmq.com/debian/ testing main

EOF

fi


cd /tmp
wget http://www.rabbitmq.com/rabbitmq-signing-key-public.asc
apt-key add rabbitmq-signing-key-public.asc

apt-key adv --keyserver keyserver.ubuntu.com --recv-keys C300EE8C
apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 8D0DC64F
apt-get update
