<?php

// Print initial create database/user statements to be piped into mysql

require_once("start.php");
require_once("scripts/cmdline.php");

Config::set('debug', false);

$dbname = Config::get('dbname');
$dbuser = Config::get('dbuser');
$dbpass = Config::get('dbpass');

echo "
CREATE DATABASE {$dbname};
";

if ($dbuser != 'root')
{
echo "
CREATE USER '{$dbuser}'@'localhost' IDENTIFIED BY '{$dbpass}';
GRANT ALL PRIVILEGES ON {$dbname}.* TO '{$dbuser}'@'localhost';
";
}

echo "
FLUSH PRIVILEGES;
";