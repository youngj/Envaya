<?php

/*
 * Installs the sphinx configuration file, based on scripts/config/sphinx.conf 
 * with settings in {{...}} replaced by actual values from envaya config.
 */

$base = dirname(__DIR__);
 
require_once "$base/scripts/cmdline.php";
require_once "$base/start.php";

umask(0);

render_config_template(
    "$base/scripts/config/sphinx.conf", 
    Config::get('sphinx:conf_dir').'/sphinx.conf'
);

$log_dir = Config::get('sphinx:log_dir');
if (!is_dir($log_dir))
{
    mkdir($log_dir, 0777, true);
}

require_once "$base/scripts/install_dataroot.php";

Sphinx::_reindex();