<?php

abstract class Cache
{
    abstract public function get($key);
    abstract public function set($key, $value, $timeout = 0);
    abstract public function delete($key);
}

class NullCache extends Cache
{
    public function get($key) { return null; }
    public function set($key, $value, $timeout = 0) { return null; }
    public function delete($key) { return null; }
}

class MemcacheCache extends Cache
{
    protected $memcache;

    public function __construct()
    {
        $memcache = new Memcache;

        global $CONFIG;
        foreach ($CONFIG->memcache_servers as $server)
        {
            $memcache->addServer($server, 11211);
        }

        $this->memcache = $memcache;
    }

    public function get($key)
    {
        $res = $this->memcache->get($key);
        if ($res === false)
        {
            return null; // be consistent with other cache backends
        }
        return $res;
    }
    public function set($key, $value, $timeout = 0)
    {
        return $this->memcache->set($key, $value, $timeout);
    }
    public function delete($key)
    {
        return $this->memcache->delete($key);
    }
}

class DatabaseCache extends Cache
{
    public function get($key)
    {
        $row = get_data_row("select * from `cache` where `key` = ? AND expires > ?", array($key, time()));

        if ($row)
        {
            return unserialize($row->value);
        }
        return null;
    }
    public function set($key, $value, $timeout = 86400)
    {
        $expires = time() + $timeout;
        $v = serialize($value);

        return insert_data("INSERT into `cache` (`key`,value,expires) VALUES (?,?,?) ON DUPLICATE KEY UPDATE value=?, expires=?", array($key, $v, $expires, $v, $expires));
    }
    public function delete($key)
    {
        return insert_data("DELETE FROM `cache` where `key` = ?", array($key));
    }
}

function make_cache_key()
{
    $args = func_get_args();

    global $CONFIG;
    $key = implode(":", $args) . ":{$CONFIG->cache_version}";

    if (strlen($key) > 250)
        $key = md5($key);

    return $key;
}

function get_cache()
{
    static $cache;

    if (!isset($cache))
    {
        global $CONFIG;
        $cls = $CONFIG->cache_backend;
        $cache = new $cls();
    }
    return $cache;
}
