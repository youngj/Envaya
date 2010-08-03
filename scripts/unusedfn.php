<?php

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

$dir = dirname(__DIR__);

checkDir("$dir/engine/lib", 'getDeclaredFunctions');
checkDir($dir, 'countCalledFunctions');

foreach ($functionCount as $functionName => $count)
{
    if ($count == 0)
    {
        echo "$functionName\n";
    }
}