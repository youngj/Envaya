<?php

class LogEntry extends Model
{
    static $table_name = 'log_entries';
    static $table_attributes = array(
        'object_id' => null,
        'subtype_id' => null,
        'event_name' => '',
        'user_guid' => 0,
        'time_created' => 0,
    );
    
    static $logcache = array();

    function get_user_entity()
    {
        return User::get_by_guid($this->user_guid, true);
    }
    
    function get_model_object()
    {
        $subtype_id = $this->subtype_id;
        
        $cls = $subtype_id ? ClassRegistry::get_class($subtype_id) : null;                
        if ($cls)
        {
            $pk = $cls::$primary_key;
            return $cls::query()->where("`$pk` = ?", $this->object_id)->get();
        }
        else
        {
            return null;
        }
    }    
    
    static function create($event_name, $model_obj = null)
    {
        if ($model_obj)
        {
            $object_id = $model_obj->get_primary_key(); // assumed to be integer or bigint            
            $subtype_id = $model_obj->get_subtype_id();
        }
        else
        {
            $object_id = null;
            $subtype_id = null;
        }
                
        $logcache_key = "{$event_name}_{$object_id}_{$subtype_id}";

        if (!isset(static::$logcache[$logcache_key]))
        {
            $time = timestamp();
            $user = Session::get_logged_in_user(); 
        
            Database::update("INSERT DELAYED into log_entries (
                object_id, subtype_id, event_name, user_guid, time_created)
                VALUES (?,?,?,?,?)",
                array($object_id, $subtype_id, $event_name, ($user ? $user->guid : 0), $time)
            );
            
            static::$logcache[$logcache_key] = true;
        }
    }
}