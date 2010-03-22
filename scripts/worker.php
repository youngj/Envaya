<?php

require_once("scripts/cmdline.php");

require_once("engine/start.php");

$startTime = time();
$maxWorkerTime = 60;

while (time() - $startTime < $maxWorkerTime)
{
    if (!exec_queued_function_call($timeout = 500))
    {
        sleep(1);
    }
}
