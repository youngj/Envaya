<?php

# 
# Runs a web server and various other background tasks on development computers.
# (On a production server, these background tasks are run via /etc/init.d/ scripts.)
#

require_once "scripts/cmdline.php";
require_once "start.php";

function start_kestrel()
{
    $kestrel_data_dir = Config::get('dataroot') . '/kestrel_data';
    if (!is_dir($kestrel_data_dir))
    {
        mkdir($kestrel_data_dir, 0777, true);
    }
    
    $root = Config::get('root');        
    $kestrel_jar = "$root/vendors/kestrel/kestrel-1.2.jar";    
    $kestrel_conf = Config::get('dataroot').'/kestrel.conf';
    
    $kestrel = run_task("java -jar ".escapeshellarg($kestrel_jar)." -f ".escapeshellarg($kestrel_conf), $kestrel_data_dir);    
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

$web_server = run_task("php scripts/web_server.php");
$kestrel = start_kestrel();
$sphinx = start_sphinx();
$queueRunner = run_task("php scripts/queueRunner.php");
$cron = run_task("php scripts/cron.php");

while(true) { sleep(2); }