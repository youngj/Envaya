<?php
    $root = dirname(__DIR__);
    require_once "$root/start.php";
    $test_config = include("$root/test/config.php");     

    $dbname = $test_config['db:name'];
    $dbuser = Config::get('db:user');
    
    ob_start();
    echo "DROP DATABASE IF EXISTS $dbname;\n";
    echo "CREATE DATABASE $dbname;\n";
    echo "GRANT ALL PRIVILEGES ON {$dbname}.* TO '{$dbuser}'@'localhost';\n";    
    echo "FLUSH PRIVILEGES;\n";    
    
    $sql = ob_get_clean();    
    echo $sql;