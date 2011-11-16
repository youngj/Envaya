<?php

interface Cache
{
    function get($key);
    function set($key, $value, $timeout = 0);
    function delete($key);    
}

function make_cache_key()
{
    $args = func_get_args();

    $key = implode(":", $args) . ":" . Config::get('cache_version');

    if (strlen($key) > 250)
        $key = md5($key);

    return $key;
}

function get_cache()
{
    static $cache;

    if (!isset($cache))
    {
        $cls = Config::get('cache_backend');
        $cache = new $cls();
    }
    return $cache;
}

function cache_result($fn, $cache_key)
{
    $cache = get_cache();
    $res = $cache->get($cache_key);
    if ($res === null)
    {
        $res = array($fn());
        $cache->set($cache_key, $res);
    }
    return $res[0];
}

