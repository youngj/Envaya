<?php
    /*
     * Prints all the files containing a particular language key prefix
     */

    require_once __DIR__."/analysis/phpanalyzer.php";
    require_once __DIR__."/analysis/statemachine/languagekeys.php";

    $prefix = @$argv[1];
    if (!$prefix)
        die("Missing search query");
    
    echo "searching for uses of language key/prefix '$prefix' ...\n";
    
    $start_microtime = microtime(true);    
    
    $analyzer = new PHPAnalyzer();    
    $lang_sm = new StateMachine_LanguageKeys();    
    $analyzer->add_state_machine($lang_sm);
    
    $analyzer->parse_dir(dirname(__DIR__));
    
    $all_paths = array();
    
    foreach ($lang_sm->lang_keys as $k => $paths)
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
    exit;
    