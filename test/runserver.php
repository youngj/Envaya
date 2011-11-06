<?php
    /*
     * Runs Envaya's server processes using the test configuration (e.g. so developers can examine the resulting state
     * at the end of a test suite)
     */

    $root = dirname(__DIR__);

    require_once "$root/scripts/cmdline.php";

    $env = get_environment();    
    $env["ENVAYA_CONFIG"] = json_encode(include __DIR__."/config.php");        
    $runserver = run_task('php runserver.php', $root, $env);
    
    $runserver = run_task('php runserver.php', $root, $env);