<?php

class Config
{
    private static $settings = null;
    
    static function get($key)
    {
        return static::$settings[$key];
    }
    
    static function set($key, $value)
    {
        static::$settings[$key] = $value;
    }
    
    static function load()
    {
        if (static::$settings == null)
        {
            static::$settings = array();
            static::load_group('default');
            static::load_group('local');
            static::load_group('dependent');
        }
    }

    private static function load_array($config_array)
    {
        if ($config_array)
        {
            foreach ($config_array as $k => $v)
            {
                static::set($k, $v);
            }
        }
    }
    
    private static function load_group($group_name)
    {
        $path = dirname(__DIR__)."/config/{$group_name}.php";                    
        if (file_exists($path))
        {
            return static::load_array(include($path));
        }
        return null;
    }    
}