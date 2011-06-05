<?php

/*
 * A script that scans the codebase for functions that are unused or
 * that differ from style conventions.
 */

require_once "scripts/cmdline.php";
require_once "scripts/analysis/functionanalyzer.php";
require_once "start.php";

$mode = @$argv[1] ?: 'all';

$checker = new FunctionAnalyzer();

$fn = "add_mode_$mode";



if (method_exists($checker, $fn))
{
    echo "running test '$mode' ...\n";
    
    $start = microtime(true);
    $checker->$fn();
    $checker->parse_dir(dirname(__DIR__));
    $checker->print_results();
    
    echo microtime(true) - $start;
    echo "\n";
}   
else
{
    echo "invalid mode $mode\n";
}       