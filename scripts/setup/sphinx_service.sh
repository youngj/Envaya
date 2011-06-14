#!/bin/bash

SETUP_DIR=$(cd `dirname $0` && pwd)

groupadd sphinx
useradd -r -d /var/log/sphinx -g sphinx -s /bin/false sphinx
mkdir -p /var/log/sphinx
chown sphinx.sphinx /var/log/sphinx
chmod 777 /var/log/sphinx

cp $SETUP_DIR/init.d/sphinxsearch /etc/init.d/sphinxsearch

chmod 755 /etc/init.d/sphinxsearch
update-rc.d sphinxsearch defaults 94
/etc/init.d/sphinxsearch start
