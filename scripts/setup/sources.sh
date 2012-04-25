#!/bin/bash

# save old sources.list file
if [ ! -f ~/sources.list.old ]; then
    cp /etc/apt/sources.list ~/sources.list.old
fi

# overwrite sources.list with our own sources

if ! grep -q sources.sh /etc/apt/sources.list ; then 

cat <<EOF >> /etc/apt/sources.list

# following sources added by setup/sources.sh

deb http://us.archive.ubuntu.com/ubuntu/ lucid main restricted
deb-src http://us.archive.ubuntu.com/ubuntu/ lucid main restricted

deb http://security.ubuntu.com/ubuntu lucid-updates main restricted
deb-src http://security.ubuntu.com/ubuntu lucid-updates main restricted

deb http://security.ubuntu.com/ubuntu lucid-security main restricted
deb-src http://security.ubuntu.com/ubuntu lucid-security main restricted

deb http://us.archive.ubuntu.com/ubuntu/ lucid universe
deb-src http://us.archive.ubuntu.com/ubuntu/ lucid universe

deb http://us.archive.ubuntu.com/ubuntu/ lucid-updates universe
deb-src http://us.archive.ubuntu.com/ubuntu/ lucid-updates universe

deb http://security.ubuntu.com/ubuntu lucid-security universe
deb-src http://security.ubuntu.com/ubuntu lucid-security universe
deb http://ppa.launchpad.net/nginx/stable/ubuntu lucid main

deb http://packages.dotdeb.org/ squeeze-php54 all

deb http://ppa.launchpad.net/brianmercer/php/ubuntu lucid main
deb-src http://ppa.launchpad.net/brianmercer/php/ubuntu lucid main

deb http://www.rabbitmq.com/debian/ testing main

# end sources added by setup/sources.sh

EOF

fi


cd /tmp
wget http://www.rabbitmq.com/rabbitmq-signing-key-public.asc
apt-key add rabbitmq-signing-key-public.asc

wget http://www.dotdeb.org/dotdeb.gpg
apt-key add dotdeb.gpg

apt-key adv --keyserver keyserver.ubuntu.com --recv-keys C300EE8C
apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 8D0DC64F
apt-get update
