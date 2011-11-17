<?php
    $root = dirname(__DIR__);
    require_once "$root/start.php";
    
    $dbname = 'envaya_test';
    $dbuser = Config::get('db:user');
    
    ob_start();
    echo "DROP DATABASE IF EXISTS $dbname;\n";
    echo "CREATE DATABASE $dbname;\n";
    echo "GRANT ALL PRIVILEGES ON {$dbname}.* TO '{$dbuser}'@'localhost';\n";    
    echo "FLUSH PRIVILEGES;\n";    
    
    $sql = ob_get_clean();    
    echo $sql;