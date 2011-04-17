<?php

class Cache_Database implements Cache
{
    public function get($key)
    {
        $row = Database::get_row("select * from `cache` where `key` = ? AND expires > ?", array($key, time()));

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

        return Database::update("INSERT into `cache` (`key`,value,expires) VALUES (?,?,?) ON DUPLICATE KEY UPDATE value=?, expires=?", array($key, $v, $expires, $v, $expires));
    }
    
    public function delete($key)
    {
        return Database::update("DELETE FROM `cache` where `key` = ?", array($key));
    }
}
