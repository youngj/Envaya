<?php

require_once "scripts/cmdline.php";
require_once "start.php";

function clear_queue($ch, $queue_name)
{
    try
    {
        while ($ch->basic_get($queue_name, true))
        {
        }
    }
    catch (AMQPChannelException $ex)
    {
    }    
}

$connection = RabbitMQ::connect();
$ch = $connection->channel();

clear_queue($ch, TaskQueue::HighPriority);
clear_queue($ch, TaskQueue::LowPriority);

$ch->close();
$connection->close();