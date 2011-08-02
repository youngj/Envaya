<?php

/*
 * Installs the kestrel configuration file, based on scripts/config/kestrel.conf 
 * with settings in {{...}} replaced by actual values from envaya config.
 */

$base = dirname(__DIR__);
 
require_once "$base/scripts/cmdline.php";
require_once "$base/start.php";

umask(0);

render_config_template(
    "$base/scripts/config/kestrel.conf", 
    Config::get('dataroot').'/kestrel.conf'
);
