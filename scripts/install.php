<?php

/* 
 * Create the initial database schema and required initial data.
 */

require_once("scripts/cmdline.php");
require_once("engine/start.php");

try
{
    Database::get_pdo();
}
catch (PDOException $ex)
{
    echo "Database error: {$ex->getMessage()}\n";
    die;        
}

function is_installed()
{
    try
    {
        return State::get('installed');
    }
    catch (DatabaseException $e)
    {
        return false;
    }
}

function run_sql_script($scriptlocation) 
{    
    ob_start();
    require $scriptlocation;
    $script = ob_get_clean();

    print "running $scriptlocation\n";
    
    $errors = array();

    $script = preg_replace('/\-\-.*\n/', '', $script);
    $sql_statements =  preg_split('/;[\n\r]+/', $script);
    foreach($sql_statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            Database::update($statement);
        }
    }
}

function install_schema($module_name = null)
{
    $path = "schema/mysql.php";
    if ($module_name)
    {
        $path = Engine::get_module_path($module_name).$path;
    }
    
    if (is_file($path))
    {
        run_sql_script($path);
    }
    else
    {   
        echo "(no schema at $path)";
    }
}

$module_name = @$argv[1];

if ($module_name)
{
    install_schema($module_name);
}
else
{    
    if (!is_installed())
    {    
        install_schema();
        
        foreach (Config::get('modules') as $module_name)
        {
            install_schema($module_name);
        }
        
        State::set('installed', 1);    
        echo "done\n";
    }
    else
    {
        echo "already installed\n";
    }
}

Config::set('debug', false); // hack to suppress SQL profiling messages for this script