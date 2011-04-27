<?php

/* 
 * Checks for common errors in language files, such as missing translations for a given language.
 * Prints results to stdout.
 *
 * To check english:
 *   php scripts/checklang.php 
 *
 * To check any other language:
 *   php scripts/checklang.php <languagecode>
 */

require_once "scripts/cmdline.php";
require_once "engine/start.php";

$lang = @$_SERVER['argv'][1] ?: 'en';

$en = Language::get('en');
$en->load_all();

$en_trans = $en->get_loaded_translations();

function get_missing_language_keys($en, $trans)
{
    $en_admin = $en->get_group('admin');
    $missing = array();
   
    foreach ($en->get_loaded_translations() as $k => $v)
    {
        if (!isset($en_admin[$k]) && !isset($trans[$k]))
        {
            $missing[] = $k;
        }
    }
    return $missing;
}

function get_placeholder_errors($en_trans, $trans)
{
    $error_keys = array();
    foreach ($trans as $k => $v)
    {
        if (isset($en_trans[$k]))
        {
            $correct_placeholders = Language::get_placeholders($en_trans[$k]);
            $placeholders = Language::get_placeholders($v);
            sort($correct_placeholders);
            sort($placeholders);
            if ($placeholders != $correct_placeholders)
            {
                $error_keys[] = $k;
            }
            
        }
    }
    return $error_keys;
}

if ($lang != 'en')
{
    $language = Language::get($lang);    
    $language->load_all();
    $trans = $language->get_loaded_translations();

    $placeholder_errors = get_placeholder_errors($en_trans, $trans);
    foreach ($placeholder_errors as $key)
    {
        echo "mismatched placeholder: $key\n";
    }
    
    $missingKeys = get_missing_language_keys($en, $trans);

    foreach ($missingKeys as $key)
    {
        echo "missing: $key\n";
    }

    echo "\n";

    echo sizeof($trans)." keys present\n";
    echo sizeof($missingKeys)." keys missing\n";

    foreach ($trans as $k => $v)
    {
        if (!isset($en_trans[$k]))
        {
            echo "extraneous: $k\n";
        }
        else if ($en_trans[$k] == $v)
        {
            echo "same as english: $k\n";
        }
    }
}
else
{
    $seenKeys = array();
    $trans = $en_trans;

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
                if (preg_match_all('/(__|add_js_string)\(["\\\']([\w\:]+)["\\\']/', $contents, $langKeys))
                {
                    foreach ($langKeys[2] as $langKey)
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
    checkDir(dirname(__DIR__));

    foreach ($seenKeys as $seenKey => $seen)
    {
        if (!isset($en_trans[$seenKey]))
        {
            echo "missing: $seenKey\n";
        }
    }
    
    foreach ($en_trans as $k => $v)
    {
        if (!isset($seenKeys[$k]) && !preg_match('/^(region|tinymce|design\:theme|lang|date\:month|viewtype)\:/', $k))
        {
            echo "maybe unused: $k\n";
        }
    }
}

$valueCount = array();
foreach ($trans as $k => $v)
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
