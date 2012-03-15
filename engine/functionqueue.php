<?php

/* 
 * Simple method for queuing functions that are executed later by a separate process (not in the current web request).
 * kestrel is used to store the queue. scripts/queueRunner.php (/etc/init.d/queueRunner) executes tasks from the queue.
 *
 * Currently provides no mechanism for tracking whether a queued function has completed, although that
 * may be inferred by checking for output. 
 */
class FunctionQueue
{
    private static $in_process_queue = array();
    private static $connect_tried = false;    
    private static $connection = null;    
    
    // queue names
    const HighPriority = 'high';
    const LowPriority = 'low';

    static function connect()
    {        
        if (!static::$connection && !static::$connect_tried)
        {
            require_once Engine::$root.'/vendors/php-amqplib/amqp.inc';
        
            static::$connect_tried = true;        
            
            $connection = new AMQPConnection(
                Config::get('amqp:host'), 
                Config::get('amqp:port'),
                Config::get('amqp:user'),
                Config::get('amqp:password'),
                Config::get('amqp:vhost')
            );            
            
            static::$connection = $connection;
        }
        return static::$connection;
    }
    
    static function is_server_available()
    {
        static::$connect_tried = false;
        try
        {
            return static::connect() != null;
        }
        catch (IOException $ex)
        {
            return false;
        }
    }

    static function queue_call($fn, $args, $queue_name = null)
    {    
        if (!$queue_name)
        {
            $queue_name = static::HighPriority;
        }
    
        $queue_entry = array('fn' => $fn, 'args' => $args);
        
        if (@$_SERVER['REQUEST_URI'])
        {
            $connection = static::connect();
        }
        else 
        {   
            // if called from command line script, 
            // just do function at end of process if kestrel isn't running
            try 
            {
                $connection = static::connect();
            }
            catch (IOException $ex)
            {   
                error_log("oops");
                $connection = null;
                Hook_EndRequest::register_handler_fn(array('FunctionQueue', 'exec_in_process_queue'));
            }
        }

        if (!$connection)
        {
            if (!static::is_already_queued_in_process($queue_entry))
            {
                static::$in_process_queue[] = $queue_entry;
            }
        }
        else
        {   
            $ch = $connection->channel();
            $ch->queue_declare($queue_name, false, true, false, false);
            
            $msg = new AMQPMessage(serialize($queue_entry), array('content_type' => 'text/plain', 'delivery-mode' => 2));            
            
            $ch->basic_publish($msg, '', $queue_name);
        }
        return true;
    }
    
    public static function exec_in_process_queue()
    {
        foreach (static::$in_process_queue as $q)
        {
            static::exec_queue_entry($q);
        }      
        static::$in_process_queue = array();
    }
    
    private static function is_already_queued_in_process($queue_entry)
    {
        foreach (static::$in_process_queue as $q)
        {
            if ($q['fn'] == $queue_entry['fn'] && $q['args'] == $queue_entry['args'])
            {
                return true;
            }
        }
        return false;
    }
    
    static function exec_queue_entry($queue_entry)
    {
        call_user_func_array($queue_entry['fn'], $queue_entry['args']);
    }   
        
    static function exec_queued_call($timeout_ms = 0, $queue_name = null)
    {    
        return false;
    }
    
    static function clear($queue_name)
    {
        $connection = static::connect();
        
        /*
        while ($kestrel->get($queue_name)) {
            error_log("deleted something from queue");
        }
        */
    }    
    
    static function get_stats()
    {
        $connection = static::connect();
        
        //return $kestrel->getStats();
    }
}