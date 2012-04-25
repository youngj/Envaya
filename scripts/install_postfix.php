<?php

$base = dirname(__DIR__);
 
require_once "$base/scripts/cmdline.php";
require_once "$base/start.php";

umask(0);

$host = Config::get('mail:upstream_smtp_host');
$port = Config::get('mail:upstream_smtp_port');
$username = Config::get('mail:upstream_smtp_user');
$password = Config::get('mail:upstream_smtp_pass');

render_config_template(
    "$base/scripts/config/main.cf", 
    '/etc/postfix/main.cf'
);

render_config_template(
    "$base/scripts/config/opendkim.conf", 
    '/etc/opendkim.conf'
);

system("newaliases");

file_put_contents('/etc/postfix/sasl_passwd', "$host:$port $username:$password");
system("postmap hash:/etc/postfix/sasl_passwd");
unlink('/etc/postfix/sasl_passwd');
system("/etc/init.d/opendkim restart");
system("/etc/init.d/postfix restart");
