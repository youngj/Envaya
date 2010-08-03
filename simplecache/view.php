<?php

/**
 * Simple cache viewer
 * Bypasses the engine to view simple cached views.
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

require_once(dirname(__DIR__). '/engine/settings.php');

function load_engine()
{
    require_once(dirname(__DIR__) . "/engine/start.php");
}

function get_cache_filename($view, $viewtype)
{
    global $CONFIG;
    return $CONFIG->dataroot . 'views_simplecache/' . md5($viewtype . $view . $CONFIG->cache_version);
}

function output_cached_view($view, $viewtype)
{        
    global $CONFIG;

    $contents = '';
    
    if (empty($viewtype))
    {
        $viewtype = 'default';
    }

    $simplecache_enabled = $CONFIG->simplecache_enabled;

    if ($simplecache_enabled)
    {
        $filename = get_cache_filename($view, $viewtype);
        if (file_exists($filename)) 
        {
            $contents = file_get_contents($filename);
            $cacheHeader = "1";
        }
        else
        {
            load_engine();
            $contents = elgg_view($view);
            if ($contents)
            {
                if (!file_exists($CONFIG->dataroot . 'views_simplecache'))
                {
                    @mkdir($CONFIG->dataroot . 'views_simplecache');
                }
                file_put_contents($filename, $contents);            
                $cacheHeader = "0";
            }
            else
            {
                header("HTTP/1.1 404 Not Found");                
                exit;
            }
        }
        
        header("X-Cache-Status: $cacheHeader\n");
    } 
    else 
    {
        load_engine();
        $contents = elgg_view($view);        
    }
        
    header("Content-Length: " . strlen($contents));

    $split_output = str_split($contents, 1024);

    foreach($split_output as $chunk)
        echo $chunk;
}