<?php

    /*
     * Web entry point for CSS files for testing.     
     */
    require_once(dirname(__DIR__) . '/engine/start.php');    
    if (Config::get('debug'))
    {
        header("Content-type: text/css", true);
        header('Expires: ' . date('r',time() + 86400000), true);
        header("Pragma: public", true);
        header("Cache-Control: public", true);
        
        echo view('css/'.(@$_GET['name'] ?: 'default'));
    }
    else
    {
        not_found();
    }
?>