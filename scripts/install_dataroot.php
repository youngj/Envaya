<?php

chdir(dirname(__DIR__));
 
require_once "scripts/cmdline.php";
require_once "start.php";

umask(0);

$dataroot = Config::get('dataroot');

if (!is_dir($dataroot))
{
    mkdir($dataroot, 0777, true);
}

$last_error_time = "$dataroot/last_error_time";

file_put_contents($last_error_time, "");
chmod($last_error_time, 0777);
