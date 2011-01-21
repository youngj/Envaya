<?php

class GeoIP
{
    static function get_country_code()
    {
        // country code should be set by nginx server module, 
        // but you can override it using _country query string parameter
        
        $override_code = @$_GET['_country'];
        
        if ($override_code)
        {
            return preg_replace('/\W/','',strtoupper($override_code));
        }        
        
        return @$_SERVER['GEOIP_COUNTRY_CODE'];
    }
    
    static function is_supported_country()
    {
        return static::get_country_code() == 'TZ';
    }
}