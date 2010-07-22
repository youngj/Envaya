<?php

include("scripts/cmdline.php");
include("engine/start.php");

$lang = $_SERVER['argv'][1];

if ($lang != 'en')
{
    $missingKeys = get_missing_language_keys($lang);

    foreach ($missingKeys as $key)
    {
        echo "$key\n";
    }

    echo "\n";

    echo sizeof($CONFIG->translations[$lang])." keys present\n";
    echo sizeof($missingKeys)." keys missing (".get_language_completeness($lang)."%)\n";

    foreach ($CONFIG->translations[$lang] as $k => $v)
    {
        if (!isset($CONFIG->translations['en'][$k]))
        {
            echo "extraneous: $k\n";
        }
        else if ($CONFIG->translations['en'][$k] == $v)
        {
            echo "same as english: $k\n";
        }
    }
}
else
{

    $seenKeys = array();

    function checkDir($dir)
    {
        global $seenKeys;
        $files = scandir($dir);

        foreach ($files as $file)
        {
            $path = "$dir/$file";

            if (endswith($path, ".php"))
            {
                //echo "$path\n";
                $contents = file_get_contents($path);
                if (preg_match_all('/__\(["\\\']([\w\:]+)["\\\']\)/', $contents, $langKeys))
                {
                    foreach ($langKeys[1] as $langKey)
                    {
                        $seenKeys[$langKey] = true;
                    }
                }
            }
            else if ($file != "." && $file != ".." && $file != ".svn" && is_dir($path))
            {
                checkDir($path);
            }
        }
    }
    checkDir(__DIR__);

    foreach ($seenKeys as $seenKey => $seen)
    {
        if (!isset($CONFIG->translations['en'][$seenKey]))
        {
            echo "missing: $seenKey\n";
        }
    }

}

$valueCount = array();
foreach ($CONFIG->translations[$lang] as $k => $v)
{
    if (!isset($valueCount[$v]))
    {
        $valueCount[$v] = array($k);
    }
    else
    {
        array_push($valueCount[$v], $k);
    }
}



echo "\n";

$duplicates = 0;
foreach ($valueCount as $v => $arr)
{
    $count = sizeof($arr);
    if ($count > 1)
    {
        echo "$count duplicate: $v\n";
        echo "    ".implode(', ',$arr)."\n";
        $duplicates += ($count - 1);
    }
}
if ($duplicates > 0)
{
    echo "$duplicates total duplicate values\n";
}
