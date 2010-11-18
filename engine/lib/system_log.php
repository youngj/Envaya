<?php

interface Loggable
{
    public function get_id();
    public function get_class_name();
    static function get_object_from_id($id);
}

function system_log_listener($event, $object_type, $object)
{
    if ($object instanceof Loggable)
    {
        SystemLog::record_event($object, $event);
    }
    return true;
}

register_event_handler('all','all','system_log_listener', 400);    
