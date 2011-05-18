<?php

/*
 * A script that scans the codebase for functions that are unused or
 * that differ from style conventions.
 */

include("scripts/cmdline.php");
include("engine/start.php");

$functionCount = array();

function getDeclaredFunctions($path)
{
    global $functionCount;
    $contents = file_get_contents($path);
    if (preg_match_all('/function\s+(\w+)/', $contents, $matches))
    {
        foreach ($matches[1] as $fnName)
        {
            $functionCount[$fnName] = 0;
        }
    }
}

$numFns = 0;
function getCamelCaseFunctions($path)
{
    global $numFns;    
    
    $contents = file_get_contents($path);
    if (preg_match_all('/function\s+(\w*[A-Z]\w*)/', $contents, $matches))
    {    
        foreach ($matches[1] as $fnName)
        {          
            $numFns++;
            echo "$path:$fnName\n";
        }
    }
}

$functionArgs = array();

function getFunctionArguments($path)
{
    global $functionArgs;

    $contents = file_get_contents($path);
    if (preg_match_all('/function\s+(\w+)\([^\)]+\)/', $contents, $matches, PREG_SET_ORDER))
    {
        foreach ($matches as $match)
        {                                       
            $functionArgs[$path.":".$match[1]] = substr_count($match[0],',') + 1;
        }
    }
}

function countCalledFunctions($path)
{
    global $functionCount;
    $contents = file_get_contents($path);
    
    foreach ($functionCount as $fnName => $count)
    {    
        if ($count == 0)
        {
            if (preg_match('/(?<!function\s)\b'.$fnName.'\b/', $contents))
            {
                $functionCount[$fnName]++;
            }
        }
    }    
}

function checkDir($dir, $callback)
{
    $files = scandir($dir);

    foreach ($files as $file)
    {
        $path = "$dir/$file";

        if (preg_match('/\.php$/', $path))
        {           
            $callback($path);
        }
        else if ($file != "." && $file != ".." && $file != ".svn" && $file != '.git' && is_dir($path))
        {
            checkDir($path, $callback);
        }
    }
}



function showUnusedFunctions()
{
    $dir = dirname(__DIR__);
    global $functionCount;
    checkDir("$dir/engine", 'getDeclaredFunctions');
    checkDir($dir, 'countCalledFunctions');

    foreach ($functionCount as $functionName => $count)
    {
        if ($count == 0 && !preg_match('/^(action_|index_)/',$functionName))
        {
            echo "$functionName\n";
        }
    }
}

function showLongFunctions()
{
    $dir = dirname(__DIR__);
    checkDir("$dir/engine", 'getFunctionArguments');

    global $functionArgs;
        
    foreach ($functionArgs as $functionName => $numArgs)
    {
        if ($numArgs > 3)
        {
            echo "$numArgs $functionName\n";
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
        case 'camel': return checkDir(dirname(__DIR__)."/engine", 'getCamelCaseFunctions');
        case 'long': return showLongFunctions();
        default: echo "invalid mode: $mode\n";
    }
}

main();
    