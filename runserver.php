<?php

# 
# Runs php based background tasks (but not a web server) on development 
# computers that do not run queueRunner, kestrel, or phpCron daemons
#

require_once("scripts/cmdline.php");
require_once("engine/config.php");

Config::load();

$kestrel = run_task("java -jar kestrel-1.2.jar -f kestrel.conf", __DIR__."/vendors/kestrel_dev");

sleep(3);

$queueRunner = run_task("php scripts/queueRunner.php");

$sphinx_bin_dir = Config::get('sphinx_bin_dir');
$sphinx_conf_dir = Config::get('sphinx_conf_dir');

$sphinxSearch = run_task(escapeshellcmd("$sphinx_bin_dir/searchd")." --config ".escapeshellarg("$sphinx_conf_dir/sphinx.conf"));

include("scripts/cron.php");
