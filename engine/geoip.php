<?php

/*
 * Interface for determining the country where the current web request likely originated,
 * and other related properties.
 */
class GeoIP
{
    // world regions
    const Africa = 1;
    const Unknown = 9;

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
            $lang_key = "country:".static::get_country_code();        
            
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
        return Geography::is_supported_country(static::get_country_code());
    }
    
    static function get_world_region()
    {
        $country_code = static::get_country_code();
        
        if (preg_match('/^(d[zj]|ao|b[jwfi]|c[mvfdgi]|t[dzn]|k[me]|e[grth]|g[qamhnw]|l[sry]|m[gwlruaz]|n[aeg]|rw|s[nlodz]|z[amw]|ug)$/', $country_code))
        {
            return static::Africa;            
        }
        return static::Unknown;
    }
}