<?php

require_once("engine/start.php");
require_once("scripts/cmdline.php");

global $CONFIG;
$CONFIG->debug = false;

echo "
CREATE DATABASE {$CONFIG->dbname};
";

if ($CONFIG->dbuser != 'root')
{
echo "
CREATE USER '{$CONFIG->dbuser}'@'localhost' IDENTIFIED BY '{$CONFIG->dbpass}';
GRANT ALL PRIVILEGES ON {$CONFIG->dbname}.* TO '{$CONFIG->dbuser}'@'localhost';
";
}

echo "
FLUSH PRIVILEGES;
";