<?php

/*
 * Installs the rabbitmq users and configuration
 */

$base = dirname(__DIR__);
 
require_once "$base/scripts/cmdline.php";
require_once "$base/start.php";

$vhost = escapeshellarg(Config::get('amqp:vhost'));

system("rabbitmqctl add_vhost $vhost");

$user = escapeshellarg(Config::get('amqp:user'));
$password = escapeshellarg(Config::get('amqp:password'));

system("rabbitmqctl add_user $user $password");

system("rabbitmqctl set_permissions -p $vhost $user \".*\" \".*\" \".*\"");
