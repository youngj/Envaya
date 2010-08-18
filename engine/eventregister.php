<?php
    class EventRegister
    {
        static $all_events = array();
        
        private static function &get_handlers($event, $object_type)
        {
            if (!isset(static::$all_events[$event]))
            {
                static::$all_events[$event] = array();
            } 

            if (!isset(static::$all_events[$event][$object_type])) 
            {
                static::$all_events[$event][$object_type] = array();
            }
            
            return static::$all_events[$event][$object_type];
        }
        
        static function register_handler($event, $object_type, $handler, $priority = 500) 
        {
            $handlers = &static::get_handlers($event, $object_type);
            while (isset($handlers[$priority])) 
            {
                $priority++;
            }
            $handlers[$priority] = $handler;
            ksort($handlers);
        }
        
        private static function trigger_handlers(&$handlers, $event, $object_type, $object)
        {
            if (!empty($handlers))
            {
                foreach($handlers as $handler) 
                {
                    if ($handler($event, $object_type, $object) === false) 
                    {
                        return false;
                    }
                }
            }        
            return true;
        }
        
        static function trigger_event($event, $object_type, $object = null) 
        {
            return static::trigger_handlers(static::get_handlers($event, $object_type), $event, $object_type, $object)
                && static::trigger_handlers(static::get_handlers('all', $object_type), $event, $object_type, $object)
                && static::trigger_handlers(static::get_handlers($event, 'all'), $event, $object_type, $object)
                && static::trigger_handlers(static::get_handlers('all', 'all'), $event, $object_type, $object);
        }
    }
