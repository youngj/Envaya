<?php

class Cache_Memcache extends Cache
{
    protected $memcache;

    public function __construct()
    {
        $memcache = new Memcache;

        foreach (Config::get('cache:memcache_servers') as $server)
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
        return $this->memcache->set($key, $value, 0, $timeout);
    }
    public function delete($key)
    {
        return $this->memcache->delete($key);
    }
}
