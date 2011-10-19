<?php

// Print initial create database/user statements to be piped into mysql

require_once("start.php");
require_once("scripts/cmdline.php");

Config::set('db_profile', false);

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

$db_backup_user = Config::get('db_backup_user');
$db_backup_password = Config::get('db_backup_password');

if ($db_backup_user != $dbuser && $db_backup_user != 'root')
{
    echo "
CREATE USER '{$db_backup_user}'@'localhost' IDENTIFIED BY '{$db_backup_password}';
GRANT SELECT, LOCK TABLES ON {$dbname}.* TO '{$db_backup_user}'@'localhost';
";
}

echo "
FLUSH PRIVILEGES;
";

