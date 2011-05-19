<?php

/* 
 * Application-level initialization and utility functions.
 */
class Engine
{
    static $init_microtime;
    
    private static $path_cache;    
    private static $autoload_actions = array();
    
    /* 
     * Loads the system configuration, all php files in engine/lib/, configures auto-load 
     * for Envaya's PHP classes in the engine/ directory, initializes error and exception
     * handlers, and initializes each module by including their start.php file.
     */
    static function init()
    {       
        static::$init_microtime = microtime(true);
        
        error_reporting(E_ERROR | E_PARSE);

        require_once __DIR__."/config.php";
        Config::load();
        
        static::$path_cache = (@include(Config::get('path')."/build/path_cache.php")) ?: array();        
        
        static::load_environment_config();    
        
        mb_internal_encoding('UTF-8');        
        spl_autoload_register(array('Engine', 'autoload'));
        
        static::include_lib_files();
                
        EventRegister::register_handler('all', 'all', 'system_log_listener', 400);
        
        set_error_handler('php_error_handler');
        set_exception_handler('php_exception_handler');
        register_shutdown_function(array('Engine', 'shutdown'));
                
        foreach (Config::get('modules') as $module_name)
        {
            require static::get_module_path($module_name)."start.php";
        } 
    }
    
    /*
     * Returns the absolute system path for a module directory
     * (with trailing slash, analogous to Config::get('path')).
     */
    static function get_module_path($module_name)
    {
        return Config::get('path')."mod/$module_name/";
    }

    /*
     * Returns the absolute system path for a virtual path     
     * (which may be in a module).     
     */
    static function get_real_path($path)
    {
        if (isset(static::$path_cache[$path]))
        {
            return Config::get('path').static::$path_cache[$path];
        }

        $core_path = Config::get('path').$path;                
        if (file_exists($core_path))
        {
            static::$path_cache[$path] = $path;            
            return $core_path;
        }
        
        foreach (Config::get('modules') as $module_name)
        {
            $module_path = static::get_module_path($module_name).$path;
            if (file_exists($module_path))
            {
                return $module_path;
            }
        }
        return null;
    }

    /*
     * Returns an array of virtual paths of the php files
     * that are included in every request.     
     */    
    static function get_lib_paths()
    {
        $base = Config::get('path');
        $lib_dir = "engine/lib";

        $paths = array();
        $handle = @opendir($base.$lib_dir);
        if ($handle)
        {
            while ($file = readdir($handle))
            {
                if (preg_match('/\.php$/', $file))
                {
                    $paths[] = "{$lib_dir}/{$file}";
                }
            }
        }
        asort($paths);
        return $paths;
    }        
        
    /*
     * Overrides config settings using the ENVAYA_CONFIG environment variable,
     * which may define settings as a JSON string.
     */            
    private static function load_environment_config()
    {
        $config_json = getenv("ENVAYA_CONFIG");
        
        if (!$config_json)
        {
            return;
        }
        $config_obj = json_decode($config_json, true);
        
        if (!$config_obj || !is_array($config_obj))
        {
            return;
        }
        
        foreach ($config_obj as $k => $v)
        {
            Config::set($k, $v);
        }
    }

    /*
     * Includes all the files that should be included on every request.
     * If build/lib_cache.php exists, it will get the list of virtual paths from there,
     * which is faster than searching the filesystem for engine/lib/*.php.
     */
    private static function include_lib_files()
    {    
        $base = Config::get('path');
        $lib_paths = (@include(Config::get('path')."/build/lib_cache.php")) ?: static::get_lib_paths();           
        foreach ($lib_paths as $lib_path)
        {
            if (!include("{$base}{$lib_path}"))
            {
                die("error including $lib_path");
            }
        }    
    }
            
    /**
     * Provides auto-loading support for classes in the engine/ directory.
     *
     * Class names are converted to file names by making the class name
     * lowercase and converting underscores to slashes:
     *
     *     // Loads engine/my/class/name.php
     *     Engine::autoload('My_Class_Name');    
     */
    static function autoload($class)
    {            
        $lclass = strtolower($class);
        $file = str_replace('_', '/', $lclass);        
        $path = static::get_real_path("engine/$file.php");    
        
        if ($path)
        {
            require $path;
            if (isset(static::$autoload_actions[$lclass]))
            {
                foreach (static::$autoload_actions[$lclass] as $action)
                {
                    $action();
                }
            }                
            return TRUE;
        }
        return FALSE;
    }
    
    /*
     * Registers a function to be executed when a certain class is autoloaded;
     * useful for modules to extend core classes.
     */
    static function add_autoload_action($class, $fn)
    {
        if (class_exists($class, false))
        {
            $fn();
        }
        else
        {
            static::$autoload_actions[strtolower($class)][] = $fn;
        }
    }

    /**
     * This function is a shutdown hook registered on startup which does nothing more than trigger a
     * shutdown event when the script is shutting down, but before database connections have been dropped etc.
     */
    static function shutdown()
    {
        EventRegister::trigger_event('shutdown', 'system');

        if (Config::get('debug'))
        {
            $uri = @$_SERVER['REQUEST_URI'] ?: '';
            error_log("Page {$uri} generated in ".(float)(microtime(true) - static::$init_microtime)." seconds");
        }
    }
}