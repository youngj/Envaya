<?php

/*
 * A long-running command line task that spawns short-lived worker processes 
 * to execute queued functions (e.g. sending emails). 
 *
 * On a server it is run as a daemon by /etc/init.d/queueRunner, but can also
 * be run directly (php scripts/queueRunner.php) in a development environment.
 *
 * Workers are short-lived so that memory leaks in tasks do not matter,
 * as the worker's memory will be reclaimed by the operating system
 * when the worker exits. This master process should use very little memory.
 */

require_once("scripts/cmdline.php");

function sig_handler($signo)
{
    global $worker;
    $status = proc_get_status($worker);
    if ($status['running'])
    {
        posix_kill(-$status['pid'], SIGTERM);
        proc_close($worker);
    }
    exit;
}
pcntl_signal(SIGTERM, "sig_handler");

while (true)
{
    $worker = run_task("php scripts/worker.php");

    if (!is_resource($worker))
    {
        die("Error spawning worker");
    }

    $status = proc_get_status($worker);
    if ($status['running'])
    {
        posix_setpgid($status['pid'], $status['pid']);
    }


    while (true)
    {
        $status = proc_get_status($worker);
        if (!$status['running'])
        {
            break;
        }
        pcntl_signal_dispatch();
        sleep(1);
    }
}