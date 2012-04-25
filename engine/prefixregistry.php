<?php

class PrefixRegistry
{
    private static $prefix_to_class = array(
        'CM' => 'Comment',
        'EF' => 'ExternalFeed',
        'ES' => 'EmailSubscription',
        'EW' => 'ExternalSite',        
        'PM' => 'Permission',
        'SE' => 'ScheduledEvent',
        'SS' => 'SMSSubscription',
        'UF' => 'UploadedFile',
        'US' => 'UserScope',
        'UR' => 'User',
        'WI' => 'Widget',
    );
    
    private static $class_to_prefix = null;
    
    static function all_classes()
    {
        return static::$prefix_to_class;
    }
    
    static function register($types)
    {
        foreach ($types as $prefix => $class_name)
        {
            static::$prefix_to_class[$prefix] = $class_name;
        }
    }
    
    static function get_class($prefix)
    {
        $cls = @static::$prefix_to_class[$prefix];
        if ($cls)
        {
            return $cls;
        }
        return null;
    }
    
    static function get_prefix($class_name)
    {
        if (static::$class_to_prefix == null)
        {
            static::$class_to_prefix = array_flip(static::$prefix_to_class);
        }
        return @static::$class_to_prefix[$class_name];
    }
}