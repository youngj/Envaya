<?php

require_once("scripts/cmdline.php");

$cronTasks = array(
    array(
        'interval' => 1, 
        'cmd' => "php scripts/checkmail.php"
    )
);

$minute = 0;

// prevents running each script until the previous instance finishes

while (true)
{
    sleep(60);        
    $minute = $minute + 1;    
    foreach ($cronTasks as $cronTask)
    {
        if ($minute % $cronTask['interval'] == 0)
        {
            $cmd = $cronTask['cmd'];
            $proc = @$cronTask['proc'];                      
            if ($proc)
            {
                $status = proc_get_status($proc);
                if ($status['running'])
                {
                    print_msg("$cmd still running, skipping");
                    continue;
                }
            }        
            $cronTask['proc'] = run_task($cmd);
        }
    }    
}