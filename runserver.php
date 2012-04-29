<?php

# 
# Runs a web server and various other background tasks on development computers.
# (On a production server, these background tasks are run via /etc/init.d/ scripts.)
#

require_once "scripts/cmdline.php";
require_once "start.php";

function start_sphinx()
{
    $sphinx_bin_dir = Config::get('sphinx:bin_dir');
    $sphinx_conf_dir = Config::get('sphinx:conf_dir');
    $sphinx_pid_dir = Config::get('sphinx:pid_dir');

    if (!is_dir($sphinx_pid_dir))
    {
       mkdir($sphinx_pid_dir, 0777, true);
    }

    $sphinx = run_task(escapeshellcmd("$sphinx_bin_dir/searchd")." --config ".escapeshellarg("$sphinx_conf_dir/sphinx.conf"));

    while (true)
    {
        if (Sphinx::is_server_available())
            break;
        echo "Waiting for sphinx to start...\n";    
        sleep(1);
    }    
    return $sphinx;
}

/*
 * We don't use the resource handles from proc_open, but they're necessary at least on Windows
 * or proc_open will execute the process synchronously.
 */

$web_server = run_task("php scripts/web_server.php");
$sphinx = start_sphinx();
$qworkers = run_task("php scripts/qworkers.php");
$cron = run_task("php scripts/cron.php");

while(true) { sleep(2); }