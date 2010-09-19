#!/bin/bash
# ubuntu 8.04 x64 lamp installation

function add_sources {
cat <<EOF  > ~/sources.list

deb http://php53.dotdeb.org stable all
deb-src http://php53.dotdeb.org stable all

EOF

cat /etc/apt/sources.list >> ~/sources.list
cp ~/sources.list /etc/apt/sources.list
}

if ! grep -q php53 /etc/apt/sources.list ; then add_sources; fi

echo sun-java5-jdk shared/accepted-sun-dlj-v1-1 select true | /usr/bin/debconf-set-selections
echo sun-java5-jre shared/accepted-sun-dlj-v1-1 select true | /usr/bin/debconf-set-selections
echo sun-java6-jdk shared/accepted-sun-dlj-v1-1 select true | /usr/bin/debconf-set-selections
echo sun-java6-jre shared/accepted-sun-dlj-v1-1 select true | /usr/bin/debconf-set-selections

apt-get update
apt-get -y --allow-unauthenticated install php5 php5-curl php5-gd php5-memcache php-pear
apt-get -y install emacs memcached stunnel4 git-core mcrypt daemon sun-java6-bin