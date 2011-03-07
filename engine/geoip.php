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
                static::$country_code = preg_replace('/\W/','',strtoupper($override_code));
            }        
            else
            {            
                static::$country_code = @$_SERVER['GEOIP_COUNTRY_CODE'];
            }
        }
        return static::$country_code;
    }
    
    static $country_name;
    
    static function get_country_name()
    {
        if (!static::$country_name)
        {
            $lang_key = "country:".strtolower(static::get_country_code());        
            
            $name = __($lang_key);
            if ($name != $lang_key)
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
    
    static function is_supported_country()
    {
        return static::get_country_code() == 'TZ';
    }
}