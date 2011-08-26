<?php

/*
 * Functions for logging user actions and other events to the database.
 */
class SystemLog
{
    static function get_loggable_object($row)
    {
        $class = $row->object_class;
        if (class_exists($class))
        {
            return $class::get_object_from_id($row->object_id);
        }
        else
        {
            return null;
        }
    }    

    static $logcache = array();

    static function query()
    {
        $query = new Query_Select('system_log');
        $query->order_by('time_created desc');
        return $query;
    }
    
    /**
     * This function creates an archive copy of the system log.
     *
     * @param int $offset An offset in seconds from now to archive (useful for log rotation)
     */    
    static function archive($offset = 0)
    {
        $offset = (int)$offset;
        $now = timestamp(); // Take a snapshot of now

        $ts = $now - $offset;

        // create table
        if (!Database::update("CREATE TABLE system_log_$now as SELECT * from system_log WHERE time_created<?", array($ts)))
            return false;

        // delete
        if (Database::delete("DELETE from system_log WHERE time_created<?", array($ts))===false)
            return false;

        // alter table to engine
        if (!Database::update("ALTER TABLE system_log_$now engine=archive"))
            return false;

        return true;
    }
        
    static function record_event($object, $event)
    {
        $object_id = (int)$object->get_id();
        $object_class = $object->get_class_name();        
        $time = timestamp();
        $user_guid = (int)@$_SESSION['guid'];

        if (!isset(static::$logcache[$object_id][$event]))
        {
            Database::update("INSERT DELAYED into system_log (
                object_id, object_class, event, user_guid, time_created)
                VALUES (?,?,?,?,?)",
                array($object_id, $object_class, $event, $user_guid, $time)
            );
            static::$logcache[$object_id][$event] = true;
        }
    }
}
