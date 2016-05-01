#!/bin/bash
#
# "apt-get -y install sphinxsearch" currently installs an older release
# but we use features from 1.10-beta (sql_attr_string)
# so we install it manually as there doesn't appear to be a apt package yet
#

SETUP_DIR=$(cd `dirname $0` && pwd)

apt-get -y install libmysqlclient-dev g++ make

cd /tmp
curl http://sphinxsearch.com/files/sphinxsearch_2.2.10-release-1~jessie_amd64.deb > sphinx.deb

apt-get -y install libpq5

dpkg -i sphinx.deb