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
 */

class Config
{
    private static $settings = null;
    private static $base_dir = null;
    
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
    
    static function init_dependent_settings()
    {
        // todo: settings that depend on other settings should probably not be allowed in Config...
        
        $domain = static::get('domain');
        $url = "http://{$domain}/";
        static::$settings['url'] = $url;
        static::$settings['secure_url'] = static::get('ssl_enabled') ? "https://{$domain}/" : $url;
    }
    
    static function load()
    {
        if (static::$settings == null)
        {
            static::$base_dir = dirname(__DIR__);            
            static::load_array(static::get_group('default'));                        
            static::load_array(static::get_group('local'));
            static::load_array(@include(static::$base_dir."/build/config.php"));
            static::init_dependent_settings();
            
            // The ENVAYA_CONFIG environment variable may define settings in a JSON string
            $json = getenv("ENVAYA_CONFIG");
            if ($json)
            {
                static::load_array(json_decode($json, true));
            }                        
        }
    }
        
    private static function load_array($settings, $overwrite = true)
    {    
        if ($settings)
        {
            $all_settings =& static::$settings;
            
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
    
    static function load_module_defaults($module_name)
    {
        static::load_array(static::get_module_defaults($module_name), false);
    }

    static function get_module_defaults($module_name)
    {
        $path = static::$base_dir."/mod/{$module_name}/config/default.php";
        return @include($path);
    }    
        
    static function get_group($group_name)
    {
        $path = static::$base_dir."/config/{$group_name}.php";
        return @include($path);
    }
}