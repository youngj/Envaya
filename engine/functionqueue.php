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
    static function _connect()
    {        
        static $kestrel;
        
        if (!isset($kestrel))
        {
            $kestrel = new Memcache;
            if (!$kestrel->connect(Config::get('queue_host'), Config::get('queue_port')))
            {
                throw new IOException(__("IOException:QueueConnectFailed"));
            }
        }
        return $kestrel;
    }

    static function queue_call($fn, $args)
    {    
        $kestrel = static::_connect();

        if (!$kestrel->set('call', serialize(array('fn' => $fn, 'args' => $args))))
        {
            throw new IOException(__("IOException:QueueAppendFailed")); 
        }
        return true;
    }

    static function exec_queued_call($timeout = 0)
    {
        $kestrel = static::_connect();

        if ($nextCallStr = $kestrel->get("call/t=$timeout"))
        {   
            $nextCall = unserialize($nextCallStr);
            call_user_func_array($nextCall['fn'], $nextCall['args']);
            return true;
        }
        return false;
    }
}