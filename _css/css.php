<?php

    require_once(dirname(__DIR__) . '/simplecache/view.php');

    header("Content-type: text/css", true);
    header('Expires: ' . date('r',time() + 86400000), true);
    header("Pragma: public", true);
    header("Cache-Control: public", true);
  
    output_cached_view('css/' . (@$_GET['name'] ?: 'default'), @$_GET['viewtype']);
?>