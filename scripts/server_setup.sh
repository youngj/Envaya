#!/bin/bash
# ubuntu 10.04

function add_php_settings {
cat <<EOF >> /etc/php5/fpm/php.ini

; envaya custom settings
error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_USER_NOTICE
date.timezone = "Europe/London"
zlib.output_compression = 1
expose_php = 0

EOF
}

if ! grep -q envaya /etc/php5/fpm/php.ini ; then add_php_settings; fi

cat <<EOF | mysql
CREATE DATABASE envaya;
CREATE USER 'web'@'localhost' IDENTIFIED BY 'f03;aoeA';
GRANT ALL PRIVILEGES ON envaya.* TO 'web'@'localhost';

CREATE USER 'dropbox'@'localhost' IDENTIFIED BY '';
GRANT SELECT, LOCK TABLES ON envaya.* TO 'dropbox'@'localhost';

FLUSH PRIVILEGES;
EOF

mkdir -p /etc/nginx/ssl
chown www-data:www-data /etc/nginx/ssl
chmod 700 /etc/nginx/ssl
cp /var/envaya/current/_media/envaya_combined.crt /etc/nginx/ssl/

mkdir -p /var/elgg-data
chmod 777 /var/elgg-data

cat <<EOF > /etc/php5/fpm/php5-fpm.conf

[global]
pid = /var/run/php5-fpm.pid
error_log = /var/log/php5-fpm.log
log_level = notice
;emergency_restart_threshold = 0
;emergency_restart_interval = 0
;process_control_timeout = 0
;daemonize = yes

[www]
listen = 127.0.0.1:9000
;listen.backlog = -1
;listen.allowed_clients = 127.0.0.1
;listen.owner = www-data
;listen.group = www-data
;listen.mode = 0666
user = www-data
group = www-data

pm = dynamic
pm.max_children = 20
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
pm.status_path = /status.php
;ping.path = /ping
;ping.response = pong
;request_terminate_timeout = 0
;request_slowlog_timeout = 0
;slowlog = /var/log/php5-fpm.log.slow
;rlimit_files = 1024
;rlimit_core = 0
;chroot = 
;chdir = /var/www
;catch_workers_output = yes
 
; Pass environment variables like LD_LIBRARY_PATH. All \$VARIABLEs are taken from
; the current environment.
; Default Value: clean env
;env[HOSTNAME] = \$HOSTNAME
;env[PATH] = /usr/local/bin:/usr/bin:/bin
;env[TMP] = /tmp
;env[TMPDIR] = /tmp
;env[TEMP] = /tmp

;php_admin_value[sendmail_path] = /usr/sbin/sendmail -t -i -f www@my.domain.com
;php_flag[display_errors] = off
;php_admin_value[error_log] = /var/log/fpm-php.www.log
;php_admin_flag[log_errors] = on
;php_admin_value[memory_limit] = 32M

EOF

cat <<EOF > /etc/nginx/sites-available/default

server {
    listen   80;
    include /etc/nginx/envaya.conf;

    location ~ \.php
    {
       include /etc/nginx/fastcgi_params;
    }
}

server {
    listen 443;
    server_name envaya.org;
    ssl on;
    ssl_certificate /etc/nginx/ssl/envaya_combined.crt;
    ssl_certificate_key /etc/nginx/ssl/envaya.org.key;
    include /etc/nginx/envaya.conf;

    location ~ \.php
    {
       fastcgi_param HTTPS on;
       include /etc/nginx/fastcgi_params;
    }      
}

EOF

cat <<EOF > /etc/nginx/fastcgi_params

fastcgi_pass 127.0.0.1:9000;
fastcgi_param SCRIPT_FILENAME /var/envaya/current/\$fastcgi_script_name;
fastcgi_param PATH_INFO \$fastcgi_script_name;

fastcgi_param  QUERY_STRING       \$query_string;
fastcgi_param  REQUEST_METHOD     \$request_method;
fastcgi_param  CONTENT_TYPE       \$content_type;
fastcgi_param  CONTENT_LENGTH     \$content_length;

fastcgi_param  SCRIPT_NAME        \$fastcgi_script_name;
fastcgi_param  REQUEST_URI        \$request_uri;
fastcgi_param  DOCUMENT_URI       \$document_uri;
fastcgi_param  DOCUMENT_ROOT      \$document_root;
fastcgi_param  SERVER_PROTOCOL    \$server_protocol;

fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
fastcgi_param  SERVER_SOFTWARE    nginx/\$nginx_version;

fastcgi_param  REMOTE_ADDR        \$remote_addr;
fastcgi_param  REMOTE_PORT        \$remote_port;
fastcgi_param  SERVER_ADDR        \$server_addr;
fastcgi_param  SERVER_PORT        \$server_port;
fastcgi_param  SERVER_NAME        \$server_name;

# PHP only, required if PHP was built with --enable-force-cgi-redirect
fastcgi_param  REDIRECT_STATUS    200;

EOF

cat <<EOF > /etc/nginx/envaya.conf

    root /var/envaya/current;
    access_log  /var/log/nginx/access.log combined_time;
    client_max_body_size 10m;
    client_body_timeout 118;
    send_timeout 124;
    
    location / {
        index  index.php;
        rewrite ^(.*)\$ /index.php\$1 last;
    }
    
    location ~ ^\/(engine|scripts|views|languages|test|vendors)\/
    {
        return 403;
    }

    location /status.nginx
    {
        stub_status on;
        access_log   off;
    }    
    
    location /_graphics/ {
        expires 1y;
    }
    location /_media/ {
        expires 1y;
        rewrite tiny_mce\.js /_media/tiny_mce/tiny_mce_gzip.php last;
    }
    
    location /_css/ {
        rewrite  ([\w]+)\.css  /_css/css.php?name=\$1  last;
    }

EOF

cat <<EOF > /etc/nginx/nginx.conf
user www-data;
worker_processes 2;

error_log  /var/log/nginx/error.log;
pid        /var/run/nginx.pid;

events {
    worker_connections  1024;
    # multi_accept on;
}

http {
    include       /etc/nginx/mime.types;

    access_log  /var/log/nginx/access.log;
    
    log_format combined_time '\$remote_addr - \$remote_user [\$time_local]  '
                    '"\$request" \$status \$body_bytes_sent '
                    '"\$http_referer" "\$http_user_agent" \$request_time';    

    sendfile        on;
    #tcp_nopush     on;

    keepalive_timeout  15;
    tcp_nodelay        on;

    gzip  on;
    gzip_disable "MSIE [1-6]\.(?!.*SV1)";

    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}

EOF

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
cp -r /var/envaya/current/vendors/kestrel_dev/libs /var/kestrel

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

/etc/init.d/nginx restart
/etc/init.d/php5-fpm start
