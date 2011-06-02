<?php

require_once "scripts/cmdline.php";
require_once "start.php";

/* 
 * A short-lived command line task that executes queued functions
 * (e.g. sending emails). 
 */
function execute_queue_worker($queue_name, $empty_poll_interval = 1, $max_worker_time = 60)
{
    $start_time = time();

    pcntl_signal(SIGTERM, "sig_handler");

    while (time() - $start_time < $max_worker_time)
    {
        if (!FunctionQueue::exec_queued_call(500, $queue_name))
        {
            for ($i = 0; $i < $empty_poll_interval; $i++)
            {
                pcntl_signal_dispatch();
                sleep(1);
            }
        }
        pcntl_signal_dispatch();
    }
}

function sig_handler($signo)
{
     exit;
}
