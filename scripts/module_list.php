<?php

$base = dirname(__DIR__);

require_once "$base/scripts/cmdline.php";
require_once "$base/start.php";

/* 
 * Lists enabled modules for the Selenium tests (which don't have direct access to the PHP code)
 */
echo implode("\n", Config::get('modules'));