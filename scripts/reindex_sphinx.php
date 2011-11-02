<?php
 
 
$base = dirname(__DIR__);
require_once "$base/scripts/cmdline.php";
require_once "$base/start.php";

Sphinx::reindex();