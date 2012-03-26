<?php

class LogEntry extends Model
{
    static $table_name = 'log_entries';
    static $table_attributes = array(
        'subject_guid' => null,
        'event_name' => '',
        'user_guid' => 0,
        'time_created' => 0,
        'message' => null,
        'ip_address' => null,
        'source' => 0,
    );
    
    const SourceWeb = 1;    
    const SourceConsole = 3;
    const SourceSMS = 4;    
    
    static $logcache = array();

    function get_source_text()
    {
        switch ($this->source)
        {
            case static::SourceWeb: return "Web";
            case static::SourceAPI: return "API";
            case static::SourceConsole: return "Console";
            default: return "?";
        }
    }
    
    function get_user_entity()
    {
        return User::get_by_guid($this->user_guid, true);
    }
    
    function get_subject_entity()
    {
        return Entity::get_by_guid($this->subject_guid, true);
    }    
    
    static function create($event_name, $subject_entity = null, $message = null)
    {
        $subject_guid = ($subject_entity) ? $subject_entity->guid : null;
                
        $logcache_key = "{$event_name}_{$subject_guid}_{$message}";

        if (!isset(static::$logcache[$logcache_key]))
        {
            $time = timestamp();
            $user = Session::get_logged_in_user(); 
            $ip_address = Request::get_client_ip();
                                
            if (!isset($_SERVER['PATH_INFO']))
            {
                $source = static::SourceConsole;
            }
            else
            {
                $session_cls = get_class(Session::get_instance());
                $source = ($session_cls == 'Session_SMS') ? static::SourceSMS : static::SourceWeb;
            }
        
            Database::update("INSERT INTO log_entries (subject_guid, event_name, user_guid, time_created, ip_address, message, source)
                VALUES (?,?,?,?,?,?,?)",
                array($subject_guid, $event_name, ($user ? $user->guid : null), $time, $ip_address, $message, $source)
            );
            
            static::$logcache[$logcache_key] = true;
        }
    }
}