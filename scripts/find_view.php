<?php
    /*
     * Prints all the files containing a particular view path prefix
     */

    require_once __DIR__."/analysis/phpanalyzer.php";
    require_once __DIR__."/analysis/statemachine/views.php";

    $prefix = @$argv[1];
    if (!$prefix)
        die("Missing search query");

    echo "searching for uses of view path/prefix '$prefix' ...\n";
    
    $start_microtime = microtime(true);    
    
    $analyzer = new PHPAnalyzer();    
    $view_sm = new StateMachine_Views();    
    $analyzer->add_state_machine($view_sm);
    
    $analyzer->parse_dir(dirname(__DIR__));
    
    $all_paths = array();
    
    foreach ($view_sm->views as $k => $paths)
    {
        if (strpos($k, $prefix) === 0)
        {
            foreach ($paths as $path)
            {
                $all_paths[$path] = true;
            }
        }        
    }   
    
    ksort($all_paths);   
    
    foreach ($all_paths as $path => $v)
    {   
        echo "$path\n";
    }
    
    echo microtime(true) - $start_microtime;
    echo " seconds \n";
