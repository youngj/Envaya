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
    static $in_process_queue = array();

    static function _connect()
    {        
        static $kestrel = null;
        static $connect_tried = false;
        
        if (!$kestrel && !$connect_tried)
        {
            $connect_tried = true;
            $k = new Memcache;
            
            if (!@$k->connect(Config::get('queue_host'), Config::get('queue_port')))
            {
                throw new IOException(__("error:QueueConnectFailed"));
            }
                        
            $kestrel = $k;
        }
        return $kestrel;
    }

    static function queue_call($fn, $args)
    {    
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
            if (!$kestrel->set('call', serialize($queue_entry)))
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
        
    static function exec_queued_call($timeout = 0)
    {
        $kestrel = static::_connect();

        if ($nextCallStr = $kestrel->get("call/t=$timeout"))
        {   
            static::exec_queue_entry(unserialize($nextCallStr));
            return true;
        }
        return false;
    }
}