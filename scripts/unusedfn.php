<?php

/*
 * A script that scans the codebase for functions that are unused or
 * that differ from style conventions.
 */

include("scripts/cmdline.php");
include("start.php");

require_once "scripts/phpanalyzer.php";
require_once "scripts/statemachine/functiondef.php";
require_once "scripts/statemachine/functioncall.php";
require_once "scripts/statemachine/functioncallback.php";

function getCamelCaseFunctions()
{
    $analyzer = new PHPAnalyzer();    
    $function_sm = new StateMachine_FunctionDef();    
    $analyzer->add_state_machine($function_sm);    
    $analyzer->parse_dir(dirname(__DIR__));
    
    foreach ($function_sm->function_info as $fullName => $info)
    {
        if (preg_match('/[A-Z]/', $info['name']))
        {
            echo "$fullName\n";
            echo "  {$info['path']}\n";
        }
    }
}

function showUnusedFunctions()
{
    $analyzer = new PHPAnalyzer();    
    $function_sm = new StateMachine_FunctionDef();    
    $fcall_sm = new StateMachine_FunctionCall();    
    $fcallback_sm = new StateMachine_FunctionCallback(); 
    $analyzer->add_state_machine($function_sm);    
    $analyzer->add_state_machine($fcall_sm);    
    $analyzer->add_state_machine($fcallback_sm);    
    
    $analyzer->parse_dir(dirname(__DIR__));
    
    $function_calls = $fcall_sm->function_calls;
    $potential_callbacks = $fcallback_sm->potential_callbacks;
    
    foreach ($function_sm->function_info as $fullName => $info)
    {
        $functionName = $info['name'];
        if (!preg_match('/^(__|action_)/', $functionName)     
            && !preg_match('#/test/#', $info['path']) 
            && !isset($function_calls[$functionName])
            && !isset($potential_callbacks[$functionName]))
        {
            echo "$fullName\n";
            echo "  {$info['path']}\n";
        }
    }
}

function showLongFunctions()
{    
    $analyzer = new PHPAnalyzer();    
    $function_sm = new StateMachine_FunctionDef();    
    $analyzer->add_state_machine($function_sm);    
    $analyzer->parse_dir(dirname(__DIR__));
    
    foreach ($function_sm->function_info as $functionName => $info)
    {
        $numArgs = sizeof($info['args']);
        if ($numArgs > 3)
        {
            echo "$numArgs $functionName\n";
            echo "  {$info['path']}\n";
        }
    }
}

function main()
{
    global $argv;
    $mode = @$argv[1] ?: 'unused';
    echo "mode = $mode\n";
    switch ($mode)
    {
        case 'unused': return showUnusedFunctions();
        case 'camel': return getCamelCaseFunctions();
        case 'long': return showLongFunctions();
        default: echo "invalid mode: $mode\n";
    }
}

main();
    