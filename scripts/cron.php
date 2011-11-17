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

$cron_file = Engine::$root . "/crontab.php";
$cronTasks = include $cron_file;

foreach (Config::get('modules') as $module_name)
{
    $cron_file = Engine::get_module_root($module_name) . "/crontab.php";
    if (is_file($cron_file))
    {
        $cronTasks = array_merge($cronTasks, include $cron_file);
    }
}

$tick = 0;
$tick_seconds = 60; // 1 minute per tick
$tick_offset = (int)(timestamp() / $tick_seconds);
$start_time = time();

while (true)
{
    $now_time = time();
    $tick = $tick + 1;    
    $next_time = $tick * $tick_seconds + $start_time;
    $sleep_time = $next_time - $now_time;
    
    if ($sleep_time > 0)
    {
        sleep($sleep_time);
    }
    
    $i = 0; // desynchronize tasks with intervals that are multiples of each other
    foreach ($cronTasks as $cronTask)
    {
        $interval = $cronTask['interval'];
        if ($interval > 0 && ($tick + $tick_offset - $i) % $interval == 0)
        {
            $cmd = $cronTask['cmd'];
            $proc = @$cronTask['proc'];
            
            // prevent running each script until the previous instance finishes
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
        $i++;
    }
}