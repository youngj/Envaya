<?php

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