<?php

/*
 * Application-level initialization and utility functions.
 */
class Engine
{
    static $used_lib_cache;

    private static $path_cache;
    static $build_cache;
    private static $loaded_classes = array();
    private static $autoload_patch = array();
    static $view_patch = array();

    static $root;
    private static $module_classes = array();

    /*
     * Loads the system configuration, all php files in /lib/, configures auto-load for
     * Envaya's PHP classes in the engine/ directory, and initializes error and exception handlers.
     */
    static function init()
    {
        self::$root = $root = dirname(__DIR__);
        self::$path_cache = @include("$root/build/path_cache.php") ?: array();    
    
        if (@include("$root/build/cache.php"))
        {
            self::$build_cache = new RealBuildCache();
        }
        else
        {
            self::$build_cache = new BuildCache();
        }    
    
        require_once __DIR__."/config.php";
        Config::load();

        mb_internal_encoding('UTF-8');

        self::include_lib_files();
        self::init_modules();

        // register autoload after module initialization
        // so that modules can't accidentally autoload anything in start.php
        spl_autoload_register(array('Engine', 'autoload'));

        // do things that depend on autoload
        set_error_handler('php_error_handler');
        set_exception_handler('php_exception_handler');

        register_shutdown_function(array('Engine', 'shutdown'));
    }

    static function init_modules()
    {
        $lower_classes = array();
        foreach (Config::get('modules') as $module_name)
        {
            require self::get_module_root($module_name)."/module.php";
            $module_class = "Module_{$module_name}";

            foreach ($module_class::$autoload_patch as $cls)
            {
                if (isset($lower_classes[$cls]))
                {
                    $lclass = $lower_classes[$cls];
                }
                else
                {
                    $lower_classes[$cls] = $lclass = strtolower($cls);
                }
                self::$autoload_patch[$lclass][] = $module_class;
            }

            foreach ($module_class::$view_patch as $view)
            {
                self::$view_patch[$view][] = $module_class;
            }
        }
    }

    /*
     * Returns the absolute system path for a module directory
     */
    static function get_module_root($module_name)
    {
        return static::$root."/mod/$module_name";
    }

    /*
     * Returns the absolute system path for a virtual path
     * (which may be in a module).
     */
    static function get_real_path($path)
    {
        $root = self::$root;
        $path_cache =& self::$path_cache;

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

        return self::filesystem_get_real_path($path);
    }

    /*
     * Returns the system absolute path for a given virtual path,
     * using the file system to search for the virtual path in each module,
     * without using the path cache.
     * (potentially O(num_modules) running time)
     */
    static function filesystem_get_real_path($path)
    {
        $root = self::$root;

        foreach (Config::get('modules') as $module_name)
        {
            $module_rel_path = "mod/{$module_name}/{$path}";
            $module_path = "{$root}/{$module_rel_path}";
            if (file_exists($module_path))
            {
                self::$path_cache[$path] = $module_rel_path;
                return $module_path;
            }
        }

        $core_path = "$root/$path";
        if (file_exists($core_path))
        {
            self::$path_cache[$path] = $path;
            return $core_path;
        }

        // use 0 as a sentinel for nonexistent files rather than null
        // since isset does not distinguish between null and nonexistent keys
        // and array_key_exists is slower
        self::$path_cache[$path] = 0;
        return null;
    }

    /*
     * Returns an array of virtual paths of the php files
     * that are included in every request.
     */
    static function get_lib_paths()
    {
        $root = self::$root;
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
        $root = self::$root;

        $lib_paths = self::$build_cache->_get_lib_paths();
        if (isset($lib_paths))
        {
            self::$used_lib_cache = true;
        }
        else
        {
            $lib_paths = self::get_lib_paths();
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
        $rel_path = $lparent = $lclass = null;
        // if RealBuildCache (build/cache.php) has a function with the same name as the autoloaded class,
        // it is expected to return the following via output reference parameters:
        // - the relative path of the php file for the class,
        // - lowercase name of the parent class
        // - and the lowercase name of the class
        self::$build_cache->$class($rel_path, $lparent, $lclass);

        if (!isset($rel_path))
        {
            // the slow way
            $lclass = strtolower($class);
            $path_part = str_replace('_', '/', $lclass);
            $path = self::get_real_path("engine/$path_part.php");

            if (!$path)
            {
                return FALSE;
            }
        }
        else
        {
            $path = self::$root."/$rel_path";
            // must autoload parent class before derived class or APC can't cache the derived class
            if (isset($lparent) && !isset(self::$loaded_classes[$lparent]))
            {
                Engine::autoload($lparent);
            }
        }

        self::$loaded_classes[$lclass] = true;

        require $path;
        if (isset(self::$autoload_patch[$lclass]))
        {
            $fn = "patch_{$class}";
            foreach (self::$autoload_patch[$lclass] as $module_class)
            {
                $module_class::$fn();
            }
        }
        return TRUE;
    }

    /**
     * This function is a shutdown hook registered on startup which does nothing more than trigger a
     * shutdown event when the script is shutting down, but before database connections have been dropped etc.
     */
    static function shutdown()
    {
        $error = error_get_last();

        if ($error)
        {
            $type = $error['type'];
            if ($type == E_ERROR || $type == E_PARSE)
            {
                notify_exception(new ErrorException(
                    $error['message'],
                    0,
                    $error['type'],
                    $error['file'],
                    $error['line']
                ));
            }
        }

        Hook_EndRequest::trigger();
    }
}

class BuildCache
{
    function __call($fn, $args)
    {
    }
}
