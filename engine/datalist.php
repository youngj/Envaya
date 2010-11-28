<?php

class Datalist
{
    static $cached_list = null;

    static function init()
    {
        if (!is_array(static::$cached_list))
        {
            $cache = get_cache();

            static::$cached_list = $cache->get('datalist');

            if (!is_array(static::$cached_list))
            {
                static::$cached_list = array();

                $result = get_data("SELECT * from datalists");
                if ($result)
                {
                    foreach ($result as $row)
                    {
                        static::$cached_list[$row->name] = $row->value;
                    }
                }

                $cache->set('datalist', static::$cached_list);
            }
        }    
    }
    
    /**
     * Get the value of a particular piece of data in the datalist
     *
     * @param string $name The name of the datalist
     * @return string|false Depending on success
     */
    static function get($name)
    {
        static::init();
        return @static::$cached_list[$name];
    }

    /**
     * Sets the value for a system-wide piece of data (overwriting a previous value if it exists)
     *
     * @param string $name The name of the datalist
     * @param string $value The new value
     * @return true
     */
    static function set($name, $value)
    {
        static::init();
        insert_data("INSERT into datalists set name = ?, value = ? ON DUPLICATE KEY UPDATE value = ?",
            array($name, $value, $value)
        );
        
        static::$cached_list[$name] = $value;

        get_cache()->set('datalist', static::$cached_list);

        return true;
    }
}