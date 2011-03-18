#
# "apt-get -y install sphinxsearch" currently installs an older release
# but we use features from 1.10-beta (sql_attr_string)
# so we install it manually as there doesn't appear to be a apt package yet
#

SCRIPT_DIR=$(cd `dirname $0` && pwd)

apt-get -y install libmysqlclient-dev g++
cp $SCRIPT_DIR/init.d/sphinxsearch /etc/init.d/sphinxsearch

cd /tmp
wget http://sphinxsearch.com/files/sphinx-1.10-beta.tar.gz
tar xzvf sphinx-1.10-beta.tar.gz
cd sphinx-1.10-beta
./configure
make install

mkdir /var/log/sphinx
chmod 777 /var/log/sphinx

cd $SCRIPT_DIR/..
php scripts/install_sphinx.php

chmod 755 /etc/init.d/sphinxsearch
update-rc.d sphinxsearch defaults 94

/etc/init.d/sphinxsearch start

echo "done"