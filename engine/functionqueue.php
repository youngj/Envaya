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
    private static $kestrel = null;    
    
    // queue names
    const HighPriority = 'high';
    const LowPriority = 'low';

    private static function _connect()
    {        
        if (!static::$kestrel && !static::$connect_tried)
        {
            static::$connect_tried = true;
            $k = new Memcache;
            
            if (!@$k->connect(Config::get('queue_host'), Config::get('queue_port')))
            {
                throw new IOException(__("error:QueueConnectFailed"));
            }
                        
            static::$kestrel = $k;
        }
        return static::$kestrel;
    }
    
    static function is_server_available()
    {
        static::$connect_tried = false;
        try
        {
            $kestrel = static::_connect();
            return $kestrel != null;
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
            $kestrel = static::_connect();
        }
        else 
        {   
            // if called from command line script, 
            // just do function at end of process if kestrel isn't running
            try 
            {
                $kestrel = static::_connect();
            }
            catch (IOException $ex)
            {   
                $kestrel = null;
                EventRegister::register_handler('shutdown', 'system', array('FunctionQueue', 'exec_in_process_queue'), 99);
            }
        }

        if (!$kestrel)
        {
            if (!static::is_already_queued_in_process($queue_entry))
            {
                static::$in_process_queue[] = $queue_entry;
            }
        }
        else
        {       
            if (!$kestrel->set($queue_name, serialize($queue_entry)))
            {
                throw new IOException(__("error:QueueAppendFailed")); 
            }
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
    
    private static function exec_queue_entry($queue_entry)
    {
        call_user_func_array($queue_entry['fn'], $queue_entry['args']);
    }   
        
    static function exec_queued_call($timeout_ms = 0, $queue_name = null)
    {
        if (!$queue_name)
        {
            $queue_name = static::HighPriority;
        }
    
        $kestrel = static::_connect();

        $key = "$queue_name/t=$timeout_ms";       
        if ($nextCallStr = @$kestrel->get($key))
        {   
            static::exec_queue_entry(unserialize($nextCallStr));
            return true;
        }
        return false;
    }
    
    static function get_stats()
    {
        $kestrel = static::_connect();
        return $kestrel->getStats();
    }
}