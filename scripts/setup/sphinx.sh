#!/bin/bash
#
# "apt-get -y install sphinxsearch" currently installs an older release
# but we use features from 1.10-beta (sql_attr_string)
# so we install it manually as there doesn't appear to be a apt package yet
#

SETUP_DIR=$(cd `dirname $0` && pwd)

apt-get -y install libmysqlclient-dev g++ make

cd /tmp
wget http://sphinxsearch.com/files/sphinx-2.0.4-release.tar.gz
tar xzvf sphinx-2.0.4-release.tar.gz
cd sphinx-2.0.4-release
./configure
make install
