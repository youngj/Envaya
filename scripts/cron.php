<?php

require_once("scripts/cmdline.php");

$cronTasks = array(
   array(
       'interval' => 720,
       'cmd' => "php scripts/backup.php"
   ),
   array(
       'interval' => 1440,
       'cmd' => "php scripts/backup_s3.php"
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
            print_msg("running $cmd");
            $cronTask['proc'] = run_task($cmd);
        }
    }
}