<?php

require_once("scripts/cmdline.php");
require_once("start.php");

umask(0);
mkdir(Config::get('dataroot'), 0777, true);

$last_error_time = Config::get('dataroot') . "/last_error_time";

file_put_contents($last_error_time, "");
chmod($last_error_time, 0777);
