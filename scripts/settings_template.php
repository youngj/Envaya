<?php
    include(__DIR__ . "/settings/default.php");

    global $CONFIG;

    $CONFIG->dbuser = 'root';
    $CONFIG->dbpass = '';
    $CONFIG->dbname = 'envaya';
    $CONFIG->dbhost = 'localhost';
    
    $CONFIG->admin_email = "admin@localhost";

    include(__DIR__ . "/settings/dependent.php");    