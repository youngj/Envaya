<?php

/*
 * Long-running script to run commands at regular intervals, 
 * while ensuring that only one instance of a command is executing at a given time
 * (in case actual command runtime is longer than the scheduled interval).
 *
 * This process does as little as possible to avoid memory leaks. 
 */

require_once "start.php";
require_once "scripts/cmdline.php";

$cron_file = Config::get('root') . "/crontab.php";
$cronTasks = include $cron_file;

foreach (Config::get('modules') as $module_name)
{
    $cron_file = Engine::get_module_root($module_name) . "/crontab.php";
    if (is_file($cron_file))
    {
        $cronTasks = array_merge($cronTasks, include $cron_file);
    }
}

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