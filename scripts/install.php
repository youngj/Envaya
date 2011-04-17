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
        return Datalist::get('installed');
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
            try {
                $result = Database::update($statement);
            } catch (DatabaseException $e) {
                $errors[] = "$statement: $e->getMessage()";
            }
        }
    }
    if (!empty($errors)) {
        $errortxt = "";
        foreach($errors as $error)
            $errortxt .= " {$error};";
        throw new DatabaseException("There were a number of issues: ". $errortxt);
    }
}

    
if (!is_installed())
{    
    run_sql_script("schema/mysql.php");
    
    foreach (Config::get('modules') as $module_name)
    {
        $module_path = get_module_path($module_name)."schema/mysql.php";
        if (is_file($module_path))
        {
            run_sql_script($module_path);
        }
    }
    
    Datalist::set('installed', 1);    
    echo "done\n";
}
else
{
    echo "already installed\n";
}

Config::set('debug', false); // hack to suppress SQL profiling messages for this script