#!/bin/bash
# ubuntu 10.04

# configures nginx + php-fpm

SETUP_DIR=$(cd `dirname $0` && pwd)
SCRIPT_DIR=`dirname $SETUP_DIR`
INSTALL_DIR=`dirname $SCRIPT_DIR`
echo "INSTALL_DIR is $INSTALL_DIR"

apt-get -y --allow-unauthenticated install php5-fpm 
apt-get -y install geoip-database nginx-full 

ln -fs /etc/php5/conf.d/php-custom.ini /etc/php5/fpm/conf.d/php-custom.ini

mkdir -p /etc/nginx/ssl
chown www-data:www-data /etc/nginx/ssl
chmod 700 /etc/nginx/ssl

mkdir -p /var/nginx/cache
chmod 777 /var/nginx/cache

cp $SETUP_DIR/conf/php-fpm.conf /etc/php5/fpm/php-fpm.conf
cp $SETUP_DIR/conf/sites-available/* /etc/nginx/sites-available/
cp $SETUP_DIR/conf/fastcgi_params /etc/nginx/fastcgi_params

touch /var/log/fpm-php.www.log
chown www-data:www-data /var/log/fpm-php.www.log
chmod 600 /var/log/fpm-php.www.log

cat <<EOF > /etc/nginx/root.conf
    root $INSTALL_DIR/www;
EOF

cp $SETUP_DIR/conf/app.conf /etc/nginx/app.conf
cp $SETUP_DIR/conf/nginx.conf /etc/nginx/nginx.conf

/etc/init.d/nginx start
/etc/init.d/nginx reload
/etc/init.d/php5-fpm start
