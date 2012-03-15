<?php

require_once "scripts/cmdline.php";
require_once "start.php";

$start_time = time();
$terminating = false;
$max_age = mt_rand(300, 360);

function shutdown($ch, $conn)
{
    $ch->close();
    $conn->close();
}

function needs_terminate()
{
    pcntl_signal_dispatch();
    
    global $start_time, $max_age, $terminating;    
    return ($terminating || (time() - $start_time > $max_age));
}

function process_message($msg) 
{
    if (needs_terminate())
    {
         return;
    }

    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

    $entry = unserialize($msg->body);    

    FunctionQueue::exec_queue_entry($entry);
}

/* 
 * A short-lived command line task that executes queued functions
 * (e.g. sending emails).
 */
function execute_queue_worker($queue_name)
{
    $consumer_tag = "qworker-{$queue_name}-".getmypid();
    //error_log("starting {$consumer_tag}");
    
    pcntl_signal(SIGTERM, "sig_handler");
    
    $connection = FunctionQueue::connect();
    
    $ch = $connection->channel();
    $ch->queue_declare($queue_name, false, true, false, false);    
    
    $res = $ch->basic_consume($queue_name, $consumer_tag, false, false, false, false, 'process_message');
    
    register_shutdown_function('shutdown', $ch, $connection);

    // Loop as long as the channel has callbacks registered
    while(count($ch->callbacks)) 
    {
        if (needs_terminate())
        {
            break;
        }
        $ch->wait();
    }    
    //error_log("{$consumer_tag} is done");
}

function sig_handler($signo)
{
    global $terminating;
    $terminating = true;
}


