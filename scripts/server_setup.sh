#!/bin/bash
# ubuntu 8.04 x64 lamp installation

function add_php_settings {
cat <<EOF >> /etc/php5/apache2/php.ini

# envaya custom settings
error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_USER_NOTICE
date.timezone = "Europe/London"
zlib.output_compression = 1
expose_php = 0

EOF
}

if ! grep -q envaya /etc/php5/apache2/php.ini ; then add_php_settings; fi

cat <<EOF | mysql
CREATE DATABASE envaya;
CREATE USER 'web'@'localhost' IDENTIFIED BY 'f03;aoeA';
GRANT ALL PRIVILEGES ON envaya.* TO 'web'@'localhost';
FLUSH PRIVILEGES;
EOF

mkdir -p /var/elgg-data
chmod 777 /var/elgg-data

cat <<EOF > /etc/apache2/sites-enabled/000-default

# envaya custom settings
ServerTokens Prod
LoadModule rewrite_module /usr/lib/apache2/modules/mod_rewrite.so
LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\" %D" combined2

NameVirtualHost *
<VirtualHost *>
        ServerAdmin admin@envaya.org

        DocumentRoot /var/envaya/current/
        <Directory />
                Options FollowSymLinks
                AllowOverride None
        </Directory>
        <Directory /var/envaya/>
                Options Indexes FollowSymLinks
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>

        <Files ~ "~">
          Order allow,deny
          Deny from all
        </Files>

        ErrorLog /var/log/apache2/error.log
        LogLevel warn

        CustomLog /var/log/apache2/access.log combined2
        ServerSignature On
</VirtualHost>
        
EOF

/etc/init.d/apache2 restart

cat <<EOF > /etc/stunnel/stunnel.conf

; Protocol version (all, SSLv2, SSLv3, TLSv1)
sslVersion = SSLv3

; Some security enhancements for UNIX systems - comment them out on Win32
chroot = /var/lib/stunnel4/
setuid = stunnel4
setgid = stunnel4
; PID is created inside chroot jail
pid = /stunnel4.pid

; Some performance tunings
socket = l:TCP_NODELAY=1
socket = r:TCP_NODELAY=1
;compression = rle

; Some debugging stuff useful for troubleshooting
;debug = 7

client = yes

; Service-level configuration
[pop3s]
accept = 127.0.0.1:110
connect = pop.gmail.com:995

[smtps]
accept = 127.0.0.1:25
connect = smtp.gmail.com:465

EOF

/etc/init.d/stunnel4 restart

mkdir -p /var/kestrel
chmod 755 /var/kestrel

groupadd kestrel
useradd -r -d /var/kestrel -g kestrel -s /bin/false kestrel
chown kestrel.kestrel /var/kestrel

cp /var/envaya/current/vendors/kestrel_dev/kestrel-1.2.jar /var/kestrel

cat <<EOF > /var/kestrel/production.conf

port = 22133
host = "127.0.0.1"

log {
  filename = "/var/kestrel/kestrel.log"
  roll = "daily"
  level = "info"
}

queue_path = "/var/kestrel/kestrel.queue"
timeout = 0
max_journal_size = 16277216
max_memory_size = 134217728
max_journal_overflow = 10

EOF

cp /var/envaya/current/scripts/init.d/kestrel /etc/init.d/kestrel
chmod 755 /etc/init.d/kestrel
update-rc.d kestrel defaults 95
/etc/init.d/kestrel start

cp /var/envaya/current/scripts/init.d/queueRunner /etc/init.d/queueRunner
chmod 755 /etc/init.d/queueRunner
update-rc.d queueRunner defaults 96
/etc/init.d/queueRunner start

cp /var/envaya/current/scripts/init.d/phpCron /etc/init.d/phpCron
chmod 755 /etc/init.d/phpCron
update-rc.d phpCron defaults 97
/etc/init.d/phpCron start

