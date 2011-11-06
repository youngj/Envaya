<?php

$root = dirname(__DIR__);

require_once "$root/start.php";
require_once "$root/scripts/cmdline.php";

$events = ScheduledEvent::query()
    ->where('next_time < ?', timestamp())
    ->filter();

foreach ($events as $event)
{
    $event->try_execute();
}
