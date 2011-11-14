<?php

/* 
 * Storage and retrieval of system-wide data values.
 * Similar idea as Config, but with keys/values persisted in the database.
 */
class State
{
    static $cached_list = null;

    static function init()
    {
        if (!is_array(static::$cached_list))
        {
            $cache = get_cache();

			$cache_key = make_cache_key('state');
			
            static::$cached_list = $cache->get($cache_key);

            if (!is_array(static::$cached_list))
            {
                static::$cached_list = array();

                $result = Database::get_rows("SELECT * from `state`");
                if ($result)
                {
                    foreach ($result as $row)
                    {
                        static::$cached_list[$row->name] = $row->value;
                    }
                }

                $cache->set($cache_key, static::$cached_list);
            }
        }    
    }
    
    /**
     * Get the value of a particular piece of global state
     */
    static function get($name)
    {
        static::init();
        return @static::$cached_list[$name];
    }

    /**
     * Sets the value for a system-wide piece of global state (overwriting a previous value if it exists)
     */
    static function set($name, $value)
    {
        static::init();
        Database::update("INSERT into `state` set name = ?, value = ? ON DUPLICATE KEY UPDATE value = ?",
            array($name, $value, $value)
        );
        
        static::$cached_list[$name] = $value;

        get_cache()->set('state', static::$cached_list);
    }
}