<?php

class Cache_Null implements Cache
{
    public function get($key) { return null; }
    public function set($key, $value, $timeout = 0) { return null; }
    public function delete($key) { return null; }
}
