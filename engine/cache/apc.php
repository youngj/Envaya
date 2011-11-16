<?php

class Cache_APC implements Cache
{
    public function get($key)
    {
        $res = apc_fetch($key);
        if ($res === false)
        {
            return null; // be consistent with other cache backends
        }
        return $res;
    }

    public function set($key, $value, $timeout = 0)
    {
        return apc_store($key, $value, $timeout);
    }

    public function delete($key)
    {
        return apc_delete($key);
    }
}
