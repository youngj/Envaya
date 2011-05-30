<?php

/*
 * Interface for accessing site configuration settings 
 * (defined in config/ directory as php files 
 *  that returnan array)
 * 
 * e.g. Config::get('setting_name')
 *
 * config/default.php -- default settings
 * config/local.php -- local machine settings, not under source control
 * config/dependent.php -- hacky way to define settings that are dependent 
 *                          on other settings
 */

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
    
    static function get_all()
    {
        return static::$settings;
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
                static::$settings[$k] = $v;
            }
        }
    }
    
    private static function load_group($group_name, $module = null)
    {
        if ($module)
        {
            $base = dirname(__DIR__) . "/mod/{$module}";
        }
        else
        {
            $base = dirname(__DIR__);
        }
        $path = "{$base}/config/{$group_name}.php";
        return static::load_array(@include($path));
    }    
}