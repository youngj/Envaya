<?php

require_once("scripts/cmdline.php");
require_once("engine/start.php");

try
{
    _get_db_link();
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

function run_sql_script($scriptlocation) {

    if ($script = file_get_contents($scriptlocation)) {

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

    } else {
        throw new DatabaseException(sprintf("couldn't find the requested database script at %s.", $scriptlocation));
    }
}

    
if (!is_installed())
{    
    run_sql_script("engine/schema/mysql.sql");
    init_site_secret();
    Datalist::set('installed', 1);    
    echo "done\n";
}
else
{
    echo "already installed\n";
}

Config::set('debug', false); // hack to suppress SQL profiling messages for this script