#!/bin/bash
#
# "apt-get -y install sphinxsearch" currently installs an older release
# but we use features from 1.10-beta (sql_attr_string)
# so we install it manually as there doesn't appear to be a apt package yet
#

SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`

apt-get -y install libmysqlclient-dev g++

cd /tmp
wget http://sphinxsearch.com/files/sphinx-1.10-beta.tar.gz
tar xzvf sphinx-1.10-beta.tar.gz
cd sphinx-1.10-beta
./configure
make install

php $SCRIPT_DIR/install_sphinx.php
