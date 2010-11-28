#!/bin/bash
# setup script for base ubuntu 10.04 installation. 

#
# StackScript Bash Library
#
# Copyright (c) 2010 Linode LLC / Christopher S. Aker <caker@linode.com>
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without modification, 
# are permitted provided that the following conditions are met:
#
# * Redistributions of source code must retain the above copyright notice, this
# list of conditions and the following disclaimer.
#
# * Redistributions in binary form must reproduce the above copyright notice, this
# list of conditions and the following disclaimer in the documentation and/or
# other materials provided with the distribution.
#
# * Neither the name of Linode LLC nor the names of its contributors may be
# used to endorse or promote products derived from this software without specific prior
# written permission.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
# EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
# OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
# SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
# INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
# TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
# BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
# CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
# ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
# DAMAGE.

###########################################################
# System
###########################################################

function system_update {
    aptitude update
    aptitude -y full-upgrade
}

function system_primary_ip {
    # returns the primary IP assigned to eth0
    echo $(ifconfig eth0 | awk -F: '/inet addr:/ {print $2}' | awk '{ print $1 }')
}

function get_rdns {
    # calls host on an IP address and returns its reverse dns

    if [ ! -e /usr/bin/host ]; then
        aptitude -y install dnsutils > /dev/null
    fi
    echo $(host $1 | awk '/pointer/ {print $5}' | sed 's/\.$//')
}

function get_rdns_primary_ip {
    # returns the reverse dns of the primary IP assigned to this system
    echo $(get_rdns $(system_primary_ip))
}

###########################################################
# Postfix
###########################################################

function postfix_install_loopback_only {
    # Installs postfix and configure to listen only on the local interface. Also
    # allows for local mail delivery

    echo "postfix postfix/main_mailer_type select Internet Site" | debconf-set-selections
    echo "postfix postfix/mailname string localhost" | debconf-set-selections
    echo "postfix postfix/destinations string localhost.localdomain, localhost" | debconf-set-selections
    aptitude -y install postfix
    /usr/sbin/postconf -e "inet_interfaces = loopback-only"
    #/usr/sbin/postconf -e "local_transport = error:local delivery is disabled"

    touch /tmp/restart-postfix
}

###########################################################
# mysql-server
###########################################################

function mysql_install {
    # $1 - the mysql root password

    if [ ! -n "$1" ]; then
        echo "mysql_install() requires the root pass as its first argument"
        return 1;
    fi

    echo "mysql-server-5.1 mysql-server/root_password password $1" | debconf-set-selections
    echo "mysql-server-5.1 mysql-server/root_password_again password $1" | debconf-set-selections
    apt-get -y install mysql-server mysql-client

    echo "Sleeping while MySQL starts up for the first time..."
    sleep 5
}

function mysql_tune {
    # Tunes MySQL's memory usage to utilize the percentage of memory you specify, defaulting to 40%

    # $1 - the percent of system memory to allocate towards MySQL

    if [ ! -n "$1" ];
        then PERCENT=40
        else PERCENT="$1"
    fi

    sed -i -e 's/^#skip-innodb/skip-innodb/' /etc/mysql/my.cnf # disable innodb - saves about 100M

    MEM=$(awk '/MemTotal/ {print int($2/1024)}' /proc/meminfo) # how much memory in MB this system has
    MYMEM=$((MEM*PERCENT/100)) # how much memory we'd like to tune mysql with
    MYMEMCHUNKS=$((MYMEM/4)) # how many 4MB chunks we have to play with

    # mysql config options we want to set to the percentages in the second list, respectively
    OPTLIST=(key_buffer sort_buffer_size read_buffer_size read_rnd_buffer_size myisam_sort_buffer_size query_cache_size)
    DISTLIST=(75 1 1 1 5 15)

    for opt in ${OPTLIST[@]}; do
        sed -i -e "/\[mysqld\]/,/\[.*\]/s/^$opt/#$opt/" /etc/mysql/my.cnf
    done

    for i in ${!OPTLIST[*]}; do
        val=$(echo | awk "{print int((${DISTLIST[$i]} * $MYMEMCHUNKS/100))*4}")
        if [ $val -lt 4 ]
            then val=4
        fi
        config="${config}\n${OPTLIST[$i]} = ${val}M"
    done

    sed -i -e "s/\(\[mysqld\]\)/\1\n$config\n/" /etc/mysql/my.cnf

    touch /tmp/restart-mysql
}

function mysql_create_database {
    # $1 - the mysql root password
    # $2 - the db name to create

    if [ ! -n "$1" ]; then
        echo "mysql_create_database() requires the root pass as its first argument"
        return 1;
    fi
    if [ ! -n "$2" ]; then
        echo "mysql_create_database() requires the name of the database as the second argument"
        return 1;
    fi

    echo "CREATE DATABASE $2;" | mysql -u root -p$1
}

function mysql_create_user {
    # $1 - the mysql root password
    # $2 - the user to create
    # $3 - their password

    if [ ! -n "$1" ]; then
        echo "mysql_create_user() requires the root pass as its first argument"
        return 1;
    fi
    if [ ! -n "$2" ]; then
        echo "mysql_create_user() requires username as the second argument"
        return 1;
    fi
    if [ ! -n "$3" ]; then
        echo "mysql_create_user() requires a password as the third argument"
        return 1;
    fi

    echo "CREATE USER '$2'@'localhost' IDENTIFIED BY '$3';" | mysql -u root -p$1
}

function mysql_grant_user {
    # $1 - the mysql root password
    # $2 - the user to bestow privileges 
    # $3 - the database

    if [ ! -n "$1" ]; then
        echo "mysql_create_user() requires the root pass as its first argument"
        return 1;
    fi
    if [ ! -n "$2" ]; then
        echo "mysql_create_user() requires username as the second argument"
        return 1;
    fi
    if [ ! -n "$3" ]; then
        echo "mysql_create_user() requires a database as the third argument"
        return 1;
    fi

    echo "GRANT ALL PRIVILEGES ON $3.* TO '$2'@'localhost';" | mysql -u root -p$1
    echo "FLUSH PRIVILEGES;" | mysql -u root -p$1

}

###########################################################
# Other niceties!
###########################################################

function goodstuff {
    # Installs the REAL vim, wget, less, and enables color root prompt and the "ll" list long alias

    aptitude -y install wget vim less
    sed -i -e 's/^#PS1=/PS1=/' /root/.bashrc # enable the colorful root bash prompt
    sed -i -e "s/^#alias ll='ls -l'/alias ll='ls -al'/" /root/.bashrc # enable ll list long alias <3
}


###########################################################
# utility functions
###########################################################

function restartServices {
    # restarts services that have a file in /tmp/needs-restart/

    for service in $(ls /tmp/restart-* | cut -d- -f2); do
        /etc/init.d/$service restart
        rm -f /tmp/restart-$service
    done
}

function randomString {
    if [ ! -n "$1" ];
        then LEN=20
        else LEN="$1"
    fi

    echo $(</dev/urandom tr -dc A-Za-z0-9 | head -c $LEN) # generate a random string
}

system_update
postfix_install_loopback_only

mysql_install "root" && mysql_tune 40

goodstuff
restartServices

mysqladmin -u root -p'root' password ""

echo "deb http://ppa.launchpad.net/nginx/stable/ubuntu lucid main" >> /etc/apt/sources.list
echo "deb http://php53.dotdeb.org stable all" >> /etc/apt/sources.list
apt-key adv --keyserver keyserver.ubuntu.com --recv-keys C300EE8C

PKG_ARCH=`dpkg --print-architecture`
echo "pkg architecture is " $PKG_ARCH
mkdir -p /root/dependencies
cd /root/dependencies
wget http://us.archive.ubuntu.com/ubuntu/pool/main/k/krb5/libkrb53_1.6.dfsg.4~beta1-5ubuntu2_$PKG_ARCH.deb
wget http://us.archive.ubuntu.com/ubuntu/pool/main/i/icu/libicu38_3.8-6ubuntu0.2_$PKG_ARCH.deb
sudo dpkg -i *.deb

apt-get update
apt-get -y --allow-unauthenticated install php5-cli php5-curl php5-gd php5-memcache php-pear php5-fpm php5-mysql php5-apc
apt-get -y install nginx emacs memcached stunnel4 git-core mcrypt daemon default-jre