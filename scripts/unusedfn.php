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
    if (
            endswith($path, 'pop3.php')
      ||    endswith($path, 'smtp.php')    
      ||    endswith($path, 'socket.php')    
      ||    endswith($path, 's3.php')    
      ||    endswith($path, 'rfc822.php')    
      ||    endswith($path, 'mail.php')    
    )
        return;
    
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

        if (endswith($path, ".php"))
        {            
            $callback($path);
        }
        else if ($file != "." && $file != ".." && $file != ".svn" && is_dir($path))
        {
            checkDir($path, $callback);
        }
    }
}



function showUnusedFunctions()
{
    $dir = dirname(__DIR__);
    global $functionCount;
    checkDir("$dir/engine/lib", 'getDeclaredFunctions');
    checkDir($dir, 'countCalledFunctions');

    foreach ($functionCount as $functionName => $count)
    {
        if ($count == 0)
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

//showLongFunctions();
//showUnusedFunctions();
checkDir(dirname(__DIR__)."/engine", 'getCamelCaseFunctions');
echo "$numFns\n";