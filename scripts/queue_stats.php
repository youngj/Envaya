<?php

$root = dirname(__DIR__);

require "start.php";
require "scripts/cmdline.php";

$stats = FunctionQueue::get_stats();

if (!$stats)
{
    error_log("did not get stats from function queue");
    die;
}

echo "Uptime:             {$stats['uptime']}\n";
echo "\n";

$queues = array(
    FunctionQueue::HighPriority,
    FunctionQueue::LowPriority
);

foreach ($queues as $queue)
{
    echo "$queue:\n";
    $items = $stats["queue_{$queue}_items"];
    echo "   Current items:   {$items}\n";
    $total_items = $stats["queue_{$queue}_total_items"];
    echo "   Total items:     {$total_items}\n";
    $age = $stats["queue_{$queue}_age"];
    echo "   Max age:         {$age}\n";
    $waiters = $stats["queue_{$queue}_waiters"];
    echo "   Waiters:         {$waiters}\n";
    
    echo "\n";
}