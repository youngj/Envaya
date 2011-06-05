<?php

/* 
 * Application-level initialization and utility functions.
 */
class Engine
{
    static $used_lib_cache;
    
    private static $path_cache;    
    private static $autoload_actions = array();
    
    /* 
     * Loads the system configuration, all php files in /lib/, configures auto-load for
     * Envaya's PHP classes in the engine/ directory, and initializes error and exception handlers.
     */
    static function init()
    {       
        require_once __DIR__."/config.php";
        Config::load();        
        
        $root = Config::get('root');
        static::$path_cache = @include("$root/build/path_cache.php") ?: array();
        
        mb_internal_encoding('UTF-8');
        
        static::include_lib_files();        
        
        // initialize modules
        foreach (Config::get('modules') as $module_name)
        {
            require Engine::get_module_root($module_name)."/start.php";
        } 

        // register autoload after module initialization 
        // so that modules can't accidentally autoload anything in start.php
        spl_autoload_register(array('Engine', 'autoload'));        
        
        // do things that depend on autoload 
        set_error_handler('php_error_handler');
        set_exception_handler('php_exception_handler');                
        
        EventRegister::register_handler('all', 'all', 'system_log_listener', 400);
        register_shutdown_function(array('Engine', 'shutdown'));
    }
    
    /*
     * Returns the absolute system path for a module directory
     */
    static function get_module_root($module_name)
    {
        return Config::get('root')."/mod/$module_name";
    }

    /*
     * Returns the absolute system path for a virtual path     
     * (which may be in a module).     
     */
    static function get_real_path($path)
    {
        $root = Config::get('root');
        $path_cache =& static::$path_cache;
        
        if (isset($path_cache[$path]))
        {
            $real_path = $path_cache[$path];
            return $real_path ? "$root/$real_path" : null;
        }
        
        // load path cache for the requested virtual directory
        $cache_name = str_replace('/', '__', dirname($path));
        $dir_cache = @include("$root/build/path_cache/$cache_name.php");
        if ($dir_cache)
        {            
            foreach ($dir_cache as $virtual_path => $real_path)
            {
                $path_cache[$virtual_path] = $real_path;
            }
        }

        if (isset($path_cache[$path]))
        {
            $real_path = $path_cache[$path];
            return $real_path ? "$root/$real_path" : null;
        }
        
        // if the path_cache is working correctly, it should never get past here
        // in a release environment.

        return static::filesystem_get_real_path($path);
    }

    /*
     * Returns the system absolute path for a given virtual path,
     * using the file system to search for the virtual path in each module,
     * without using the path cache.
     * (potentially O(num_modules) running time)
     */
    static function filesystem_get_real_path($path)
    {
        $root = Config::get('root');
        $core_path = "$root/$path";                
        if (file_exists($core_path))
        {
            static::$path_cache[$path] = $path;
            return $core_path;
        }
        
        foreach (Config::get('modules') as $module_name)
        {
            $module_rel_path = "mod/{$module_name}/{$path}";
            $module_path = "{$root}/{$module_rel_path}";
            if (file_exists($module_path))
            {
                static::$path_cache[$path] = $module_rel_path;
                return $module_path;
            }
        }
        
        // use 0 as a sentinel for nonexistent files rather than null
        // since isset does not distinguish between null and nonexistent keys
        // and array_key_exists is slower        
        static::$path_cache[$path] = 0;
        return null;
    }

    /*
     * Returns an array of virtual paths of the php files
     * that are included in every request.     
     */    
    static function get_lib_paths()
    {
        $root = Config::get('root');
        $lib_dir = "lib";

        $paths = array();
        $handle = @opendir("$root/$lib_dir");
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
     * Includes all the files that should be included on every request.
     * If build/lib_cache.php exists, it will get the list of virtual paths from there,
     * which is faster than searching the filesystem for engine/lib/*.php.
     */
    private static function include_lib_files()
    {    
        $root = Config::get('root');
        $lib_paths = (@include("$root/build/lib_cache.php"));
        
        if ($lib_paths)
        { 
            static::$used_lib_cache = true;
        }
        else
        {        
            $lib_paths = static::get_lib_paths();           
        }
        
        foreach ($lib_paths as $lib_path)
        {
            if (!include("{$root}/{$lib_path}"))
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
        static::$autoload_actions[strtolower($class)][] = $fn;
    }

    /**
     * This function is a shutdown hook registered on startup which does nothing more than trigger a
     * shutdown event when the script is shutting down, but before database connections have been dropped etc.
     */
    static function shutdown()
    {
        EventRegister::trigger_event('shutdown', 'system');
    }
}