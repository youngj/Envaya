<?php

/*
 * Typically, PHP files wishing to use the Envaya engine should include this file
 * and not any others in engine/. However, engine/config.php can be loaded 
 * separately for scripts that just need to access config settings.
 *
 * (Almost everything in the engine/ directory is an auto-loaded class, 
 *   except for this file, engine.php, and the engine/lib/ directory.)
 */
  
include __DIR__."/engine.php";

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

foreach (Config::get('modules') as $module_name)
{
    require Engine::get_module_root($module_name)."/start.php";
} 
