<?php

# 
# Runs php based background tasks (but not a web server) on development 
# computers that do not run queueRunner, kestrel, or phpCron daemons
#

require_once "scripts/cmdline.php";
require_once "engine/start.php";

function start_kestrel()
{
    $kestrel = run_task("java -jar kestrel-1.2.jar -f kestrel.conf", __DIR__."/vendors/kestrel_dev");    
    while (true)
    {
        if (FunctionQueue::is_server_available())
            break;
        echo "Waiting for kestrel to start...\n";    
        sleep(1);
    }
    return $kestrel;   
}

function start_sphinx()
{
    $sphinx_bin_dir = Config::get('sphinx_bin_dir');
    $sphinx_conf_dir = Config::get('sphinx_conf_dir');
    $sphinx_pid_dir = Config::get('sphinx_pid_dir');

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

$kestrel = start_kestrel();
$sphinx = start_sphinx();
$queueRunner = run_task("php scripts/queueRunner.php");
$cron = run_task("php scripts/cron.php");

while(true) { sleep(2); }