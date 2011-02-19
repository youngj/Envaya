<?php

    /*
     * Web entry point for CSS files.     
     * The web server rewrites URLs like /_css/foo.css 
     * to /_css/css.php?name=foo.
     *
     * If 'simplecache_enabled' is true, it caches css files on disk the first 
     * time they are requested, so the engine/ php code does not need to be
     * loaded on subsequent requests.
     */

    require_once(dirname(__DIR__) . '/simplecache/view.php');

    header("Content-type: text/css", true);
    header('Expires: ' . date('r',time() + 86400000), true);
    header("Pragma: public", true);
    header("Cache-Control: public", true);
  
    output_cached_view('css/' . (@$_GET['name'] ?: 'default'), @$_GET['viewtype']);
?>