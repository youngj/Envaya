<?php

require_once "scripts/cmdline.php";
require_once "start.php";

/* 
 * A short-lived command line task that executes queued functions
 * (e.g. sending emails). 
 */
function execute_queue_worker($queue_name, $empty_poll_interval = 1.0, $max_worker_time = 60)
{
    $start_time = time();

    pcntl_signal(SIGTERM, "sig_handler");

    while (time() - $start_time < $max_worker_time)
    {
        $queue_time = microtime(true);
        
        // kestrel timeout has to be less than 1000ms (php memcache library internal timeout)
        if (!FunctionQueue::exec_queued_call(750, $queue_name))
        {
            $sleep_interval = $empty_poll_interval - (microtime(true) - $queue_time);
            
            if ($sleep_interval > 0)
            {
                pcntl_signal_dispatch();
                usleep($sleep_interval * 1000000);
            }
        }
        pcntl_signal_dispatch();
    }
}

function sig_handler($signo)
{
     exit;
}
