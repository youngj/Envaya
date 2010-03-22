<?php

require_once("scripts/cmdline.php");

while (true)
{    
    $worker = run_task("php scripts/worker.php");

    if (!is_resource($worker))
    {       
        die("Error spawning worker");
    }
    
    while (true)
    {
        $status = proc_get_status($worker);
        if (!$status['running'])
        {
            break;
        }   
        sleep(1);   
    }
}