<?php

/*
 * Installs the sphinx configuration file, based on scripts/config/sphinx.conf 
 * with settings in {{...}} replaced by actual values from envaya config.
 */

chdir(dirname(__DIR__));
 
require_once "scripts/cmdline.php";
require_once "start.php";

umask(0);

$sphinx_conf_template = file_get_contents('scripts/config/sphinx.conf');

$replacements = array();
foreach (Config::get_all() as $k => $v)
{
    $replacements["{{".$k."}}"] = $v;
}

$sphinx_conf = strtr($sphinx_conf_template, $replacements);

$conf_file = Config::get('sphinx_conf_dir').'/sphinx.conf';

if (file_put_contents($conf_file, $sphinx_conf))
{
    echo "Wrote $conf_file\n";
}
else
{
    echo "Error writing $conf_file\n";
}

$log_dir = Config::get('sphinx_log_dir');
if (!is_dir($log_dir))
{
    mkdir($log_dir, 0777, true);
}

require_once "scripts/install_dataroot.php";

Sphinx::_reindex();