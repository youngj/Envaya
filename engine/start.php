<?php

global $START_MICROTIME;
$START_MICROTIME = microtime(true);

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

    global $CONFIG;

    $path = $CONFIG->path."engine/$file.php";       
    
    if (is_file($path))
    {
        require $path;
        return TRUE;
    }

    return FALSE;
}

/**
 * This function is a shutdown hook registered on startup which does nothing more than trigger a
 * shutdown event when the script is shutting down, but before database connections have been dropped etc.
 *
 */
function __shutdown_hook()
{
    global $CONFIG, $START_MICROTIME;

    trigger_event('shutdown', 'system');

    if ($CONFIG->debug)
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
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

function bootstrap()
{   
    if (!include_once(__DIR__."/settings.php"))
    {
        echo "Error: Could not load the settings file.";
        exit;        
    }
    
    mb_internal_encoding('UTF-8');        
    spl_autoload_register('auto_load');
    
    global $CONFIG;

    foreach(get_library_files(__DIR__ . "/lib") as $file)
    {
        /*
        if (isset($CONFIG->debug) && $CONFIG->debug)
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

    init_languages();
    
    if (@$_GET['lang'])
    {
        change_viewer_language($_GET['lang']);
    }    
    
    trigger_event('init', 'system');        
}

bootstrap();
//error_log("start.php finished in ".(microtime(true) - $START_MICROTIME)." seconds");