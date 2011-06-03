<?php

/*
 * File that can be included by any PHP script to initialize the Envaya engine.
 *
 * Typically, PHP files wishing to use the Envaya engine should include this file
 * and not any files in engine/, which will be auto-loaded when needed.
 *
 * However, engine/config.php can be loaded separately for scripts that just 
 * need to access config settings.
 */
  
include __DIR__."/engine/engine.php";

/*
 * Functions to call once immediately after certain classes are loaded,
 * to do static initialization. (Modules can also use add_autoload_action
 * to extend core classes.)
 */
Engine::add_autoload_action('Database', function() { 
    Database::init(); 
});

Engine::add_autoload_action('Language', function() {
    foreach (Config::get('languages') as $code => $lang_name)
    {
        Language::init($code)->add_translations(
            array("lang:$code" => $lang_name)
        );
    }            
});

Engine::init();

// load modules
foreach (Config::get('modules') as $module_name)
{
    require Engine::get_module_root($module_name)."/start.php";
} 
