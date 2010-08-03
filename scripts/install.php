<?php

require_once("scripts/cmdline.php");
require_once("engine/start.php");
    
function is_installed()
{
    global $CONFIG;
    try
    {
        return datalist_get('installed');
    }
    catch (DatabaseException $e)
    {
        return false;
    }
}

function run_sql_script($scriptlocation) {

    if ($script = file_get_contents($scriptlocation)) {

        global $CONFIG;

        $errors = array();

        $script = preg_replace('/\-\-.*\n/', '', $script);
        $sql_statements =  preg_split('/;[\n\r]+/', $script);
        foreach($sql_statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $result = update_data($statement);
                } catch (DatabaseException $e) {
                    $errors[] = "$statement: $e->getMessage()";
                }
            }
        }
        if (!empty($errors)) {
            $errortxt = "";
            foreach($errors as $error)
                $errortxt .= " {$error};";
            throw new DatabaseException(__('DatabaseException:DBSetupIssues') . $errortxt);
        }

    } else {
        throw new DatabaseException(sprintf(__('DatabaseException:ScriptNotFound'), $scriptlocation));
    }
}

    
if (!is_installed())
{    
    run_sql_script("engine/schema/mysql.sql");
    init_site_secret();
    datalist_set('installed', 1);
    echo "done";
}
else
{
    echo "already installed";
}