<?php

interface Loggable
{
    public function getSystemLogID();
    public function getClassName();
    static function getObjectFromID($id);
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
