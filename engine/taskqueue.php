<?php

class TaskQueue
{
    // queue names
    const HighPriority = 'task-highpri';
    const LowPriority = 'task-lowpri';

    static function queue_task($callback, $args, $queue_name = null)
    {    
        if (!$queue_name)
        {
            $queue_name = static::HighPriority;
        }
        
        if (!is_array($callback))
        {
            throw new InvalidParameterException("TaskQueue callback must include class and function");
        }
    
        $cls = $callback[0];
        $fn = $callback[1];
    
        $subtype_id = ClassRegistry::get_subtype_id($cls);
        
        if (!$subtype_id)
        {
            throw new InvalidParameterException("Class $cls not in ClassRegistry");
        }
    
        if (!$fn)
        {
            throw new InvalidParameterException("Missing function name in callback");
        }
    
        $queue_entry = array('subtype_id' => $subtype_id, 'fn' => $fn, 'args' => $args);
        
        $connection = RabbitMQ::connect();

        $ch = $connection->channel();
        $ch->queue_declare($queue_name, false, true, false, false);
        
        $msg = new AMQPMessage(json_encode($queue_entry), array('content_type' => 'application/json', 'delivery-mode' => 2));            
        
        $ch->basic_publish($msg, '', $queue_name);
        return true;
    }    
    
    static function exec_queued_task($msg)
    {        
        // ack first, since we don't retry on exceptions (probably would keep failing)
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        $queue_entry = json_decode($msg->body, true);
    
        $subtype_id = $queue_entry['subtype_id'];        
        
        $cls = ClassRegistry::get_class($subtype_id);
        
        // all queued calls must have a class in class registry 
        // to limit scope of what attacker could do if they could queue their own tasks
        if (!$cls)
        {
            throw new InvalidParameterException("Subtype id $subtype_id not in ClassRegistry");
        }

        $fn = "{$queue_entry['fn']}";
        $args = $queue_entry['args'];
        
        call_user_func_array(array($cls, $fn), $args);
    }    
    
}