<?php

/*
 * Interface for determining the country where the current web request likely originated,
 * and other related properties.
 */
class GeoIP
{
    static $country_code;

    static function get_country_code()
    {
        // country code should be set by nginx server module, 
        // but you can override it using _country query string parameter
        if (!static::$country_code)
        {
            $override_code = @$_GET['_country'];
            
            if ($override_code)
            {
                static::$country_code = preg_replace('/\W/','',strtolower($override_code));
            }        
            else
            {            
                static::$country_code = strtolower(@$_SERVER['GEOIP_COUNTRY_CODE'] ?: '');
            }
        }
        return static::$country_code;
    }
    
    static $country_name;
    
    static function get_country_name()
    {
        if (!static::$country_name)
        {
            $name = Geography::get_country_name(static::get_country_code());
            
            if ($name)
            {            
                static::$country_name = $name;
            }
            else
            {
                static::$country_name = @$_SERVER['GEOIP_COUNTRY_NAME'];
            }
        }
        return static::$country_name;
    }
    
    static function get_world_region()
    {
        return Geography::get_world_region(static::get_country_code());
    }
}