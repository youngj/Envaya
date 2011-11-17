<?php

abstract class Cache
{
    abstract function get($key);
    abstract function set($key, $value, $timeout = 0);
    abstract function delete($key);   
    
    static $cache;

    static function get_instance()
    {
        if (!isset(self::$cache))
        {
            $cls = Config::get('cache:backend');
            self::$cache = new $cls();
        }
        return self::$cache;
    }
    
    static function make_key()
    {
        $args = func_get_args();

        $key = implode(":", $args) . ":" . Config::get('cache:version');

        if (strlen($key) > 250)
            $key = md5($key);

        return $key;
    }
    
    function cache_result($fn, $cache_key)
    {
        $res = $this->get($cache_key);
        if (!isset($res))
        {
            $res = array($fn());
            $this->set($cache_key, $res);
        }
        return $res[0];
    }
}