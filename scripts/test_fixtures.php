<?php
/*
 * Installs test data required for selenium tests. Recommended for development computers,
 * not to be used on production servers.
 */

$root = dirname(__DIR__);
require_once "$root/start.php";
require_once "$root/scripts/cmdline.php";

function get_test_fixtures()
{   
    $fixtures_path = 'test/fixtures.php';
    
    $core_path = Engine::$root . "/$fixtures_path";
    
    $fixtures = array();
    
    $fixtures[$core_path] = include($core_path);
    
    foreach (Config::get('modules') as $module)
    {
        $module_path = Engine::get_module_root($module)."/$fixtures_path";        
        if (is_file($module_path))
        {
            $fixtures[$module_path] = include($module_path);
        }
    }
    return $fixtures;
}

function install_test_fixtures()
{
    foreach (get_test_fixtures() as $path => $fn)
    {
        error_log("installing fixtures from $path...");
        $fn();
    }
}

function get_or_create_user($username, $class)
{
    $user = $class::get_by_username($username);
    if (!$user)
    {
        $user = new $class();
        $user->username = $username;
        $user->save();
    }
    return $user;
}

function get_or_create_org($username)
{
    $org = get_or_create_user($username, 'Organization');
    $org->set_defaults();
    $org->init_default_widgets();
    return $org;
}
