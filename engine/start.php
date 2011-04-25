<?php

/*
 * Bootstraps the Envaya engine.
 * - Loads all php files in engine/lib/. 
 * - Configures auto-load for Envaya's PHP classes in the engine/ directory.
 *   (almost everything in the engine/ directory is an
 *    auto-loaded class, except for this file and the engine/lib/ directory)
 *
 * Typically, PHP files wishing to use the Envaya engine should include this file
 * and not any others in engine/. However, engine/config.php can be loaded 
 * separately for scripts that just need to access config settings.
 *
 */

global $START_MICROTIME;
$START_MICROTIME = microtime(true);

error_reporting(E_ERROR | E_PARSE);

/**
 * Provides auto-loading support of  classes
 *
 * Class names are converted to file names by making the class name
 * lowercase and converting underscores to slashes:
 *
 *     // Loads engine/my/class/name.php
 *     auto_load('My_Class_Name');
 *
 * @param   string   class name
 * @return  boolean
 */
function auto_load($class)
{        
    $file = str_replace('_', '/', strtolower($class));    
    
    $path = get_real_path("engine/$file.php");
    if ($path)
    {
        require $path;
        return TRUE;
    }
    return FALSE;
}

function get_module_path($module_name)
{
    return Config::get('path')."mod/$module_name/";
}

function get_real_path($path)
{
    $core_path = Config::get('path').$path;            
    if (file_exists($core_path))
    {
        return $core_path;
    }
    
    foreach (Config::get('modules') as $module_name)
    {
        $module_path = get_module_path($module_name).$path;
        if (file_exists($module_path))
        {
            return $module_path;
        }
    }
    return null;
}

/**
 * This function is a shutdown hook registered on startup which does nothing more than trigger a
 * shutdown event when the script is shutting down, but before database connections have been dropped etc.
 *
 */
function __shutdown_hook()
{
    global $START_MICROTIME;

    trigger_event('shutdown', 'system');

    if (Config::get('debug'))
    {
        $uri = @$_SERVER['REQUEST_URI'] ?: '';
        error_log("Page {$uri} generated in ".(float)(microtime(true)-$START_MICROTIME)." seconds");
    }
}

function endswith( $str, $sub )
{
    return substr($str, strlen($str) - strlen($sub)) == $sub;
}

function get_library_files($directory) 
{
    $file_list = array();
    
    if ($handle = opendir($directory))
    {
        while ($file = readdir($handle))
        {
            if (endswith($file, '.php'))
            {
                $file_list[] = $directory . "/" . $file;
            }
        }
    }
    asort($file_list);
    return $file_list;
}

function register_event_handler($event, $object_type, $handler, $priority = 500)
{
    return EventRegister::register_handler($event, $object_type, $handler, $priority);
}

function load_environment_config()
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

function bootstrap()
{   
    require_once __DIR__."/config.php";
    Config::load();
    
    load_environment_config();    
    
    mb_internal_encoding('UTF-8');        
    spl_autoload_register('auto_load');
        
    foreach(get_library_files(__DIR__ . "/lib") as $file)
    {
        /*
        if (Config::get('debug'))
            error_log("Loading $file...");
        */

        if (!include_once($file))
        {
            echo "Could not load {$file}";
            die;
        }
    }
    
    set_error_handler('php_error_handler');
    set_exception_handler('php_exception_handler');
    register_shutdown_function('__shutdown_hook');
    
    Database::init();
    init_languages();
    
    foreach (Config::get('modules') as $module_name)
    {
        require_once get_module_path($module_name)."start.php";
    }
    
    if (@$_GET['lang'])
    {
        change_viewer_language($_GET['lang']);
    }    
    
    if (@$_GET['__sv'])
    {
        $view = @$_GET['view'];
        if (in_array($view, array('mobile','default')))
        {    
            set_cookie('view', $view);
        }
    }
    
    // work around flash uploader cookie bug, where the session cookie is sent as a POST field
    // instead of as a cookie
    if (@$_POST['session_id'])
    {
        $_COOKIE['envaya'] = $_POST['session_id'];
    }  
    
    trigger_event('init', 'system');      
}

bootstrap();
//error_log("start.php finished in ".(microtime(true) - $START_MICROTIME)." seconds");