<?php
    define('externalpage',true);
    
    global $viewinput;
    $viewinput['view'] = 'css/' . (@$_GET['name'] ?: 'default');
    $viewinput['viewtype'] = $_GET['viewtype'];

    header("Content-type: text/css", true);
    header('Expires: ' . date('r',time() + 86400000), true);
    header("Pragma: public", true);
    header("Cache-Control: public", true);

    require_once(dirname(dirname(__FILE__)) . '/simplecache/view.php');    
?>