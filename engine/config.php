<?php

/*
 * Interface for accessing site configuration settings 
 * (defined in config/ directory as php files 
 *  that return an array)
 * 
 * e.g. Config::get('setting_name')
 *
 * config/default.php -- default settings
 * config/local.php -- local machine settings, not under source control
 */

class Config
{
    private static $settings = null;
    private static $loaded_groups = array();      
    
    static function get($key)
    {
        if (isset(static::$settings[$key]))
        {
            return static::$settings[$key];
        }
        
        $key_arr = explode(':', $key, 2);
        if (sizeof($key_arr == 2))        
        {
            $group_name = $key_arr[0];
            
            /*           
            $local_group = "local_{$group_name}";
            if (!isset(self::$loaded_groups[$local_group]))
            {
                self::$loaded_groups[$local_group] = true;
                $local_settings = self::get_root_group($local_group);
                if (isset($local_settings))
                {
                    self::load_array($local_settings);
                    if (isset(static::$settings[$key]))
                    {
                        return static::$settings[$key];
                    }
                }
            }
            */
            
            $default_group = "default_{$group_name}";
            if (!isset(self::$loaded_groups[$default_group]))
            {            
                self::$loaded_groups[$default_group] = true;
                $default_settings = self::get_group($default_group);
                if (isset($default_settings))
                {
                    self::load_array($default_settings, false); // don't overwrite local settings with default settings
                    
                    if (isset(static::$settings[$key]))
                    {
                        return static::$settings[$key];
                    }
                }
            }
        }        
        return null;
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
            self::load_array(self::get_root_group('default'));                        
            self::load_array(self::get_root_group('local'));
            
            // The ENVAYA_CONFIG environment variable may define settings in a JSON string
            $json = getenv("ENVAYA_CONFIG");
            if ($json)
            {
                self::load_array(json_decode($json, true));
            }                        
        }
    }
        
    private static function load_array($settings, $overwrite = true)
    {    
        if ($settings)
        {
            $all_settings =& self::$settings;
            
            if (!$all_settings)
            {
                $all_settings = $settings;
            }        
            else if ($overwrite)
            {
                foreach ($settings as $k => $v)
                {                
                    $all_settings[$k] = $v;
                }
            }
            else
            {
                foreach ($settings as $k => $v)
                {                
                    if (!isset($all_settings[$k]))
                    {
                        $all_settings[$k] = $v;
                    }
                }
            }
        }
    }
    
    static function get_group($group_name)
    {
        $path = Engine::get_real_path("config/{$group_name}.php");
        return $path ? @include($path) : null;
    }
    
    static function get_root_group($group_name)
    {
        $path = Engine::$root."/config/{$group_name}.php";
        return @include($path);
    }      
}