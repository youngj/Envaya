<?php

class Geography
{
    /**
     * Interface for Google's geocode API, associating free-text place names with latitude/longitude.
     * 
     * Encode a location into a latitude and longitude, caching the result.
     *
     * @param String $location The location, e.g. "London", or "24 Foobar Street, Gotham City"
     */
    static function geocode($location)
    {
        $cached_location = Database::get_row("SELECT * from geocode_cache WHERE location=?", array($location));
        if ($cached_location)
        {
            return array(
                'lat' => $cached_location->lat, 
                'long' => $cached_location->long
            );
        }

        $return = static::google_geocode($location);

        // If returned, cache and return value
        if ($return)
        {
            $lat = (float)$return['lat'];
            $long = (float)$return['long'];

            // Put into cache at the end of the page since we don't really care that much
            Database::execute_delayed(
                "INSERT DELAYED INTO geocode_cache (location, lat, `long`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE lat=?, `long`=?",
                array($location, $lat, $long, $lat, $long)
            );
        }

        return $return;
    }
    
    static function google_geocode($location)
    {        
        $google_api = Config::get('google_api_key');
        
        $address = "http://maps.google.com/maps/geo?q=".urlencode($location)."&output=json&key=" . $google_api;

        $result = file_get_contents($address);
        $obj = json_decode($result);

        $obj = @$obj->Placemark[0]->Point->coordinates;

        if ($obj)
        {
            return array('lat' => $obj[1], 'long' => $obj[0]);
        }
        return null;
    }
    
    static function get_country_codes()
    {
        // lowercase version of http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2 code;
        // same as used by GeoIP class
        return array('tz','rw','us');
    }
    
    static function get_country_options()
    {
        $options = array();
        foreach (static::get_country_codes as $country)
        {
            $options[$country] = __("country:$country");
        }
        asort($options);
        return $options;
    }
    
    static function get_region_codes($country_code)
    {
        if ($country_code == 'tz'  || true)
        {
            return array(
                'region:tz:arusha',
                'region:tz:dar',
                'region:tz:dodoma',
                'region:tz:iringa',
                'region:tz:kagera',
                'region:tz:kigoma',
                'region:tz:kilimanjaro',
                'region:tz:lindi',
                'region:tz:manyara',
                'region:tz:mara',
                'region:tz:mbeya',
                'region:tz:morogoro',
                'region:tz:mtwara',
                'region:tz:mwanza',
                'region:tz:pemba_n',
                'region:tz:pemba_s',
                'region:tz:pwani',
                'region:tz:rukwa',
                'region:tz:ruvuma',
                'region:tz:shinyanga',
                'region:tz:singida',
                'region:tz:tabora',
                'region:tz:tanga',
                'region:tz:zanzibar_cs',
                'region:tz:zanzibar_n',
                'region:tz:zanzibar_w',
            );
        }
        else
        {
            return array();
        }    
    }
    
    /*
     * Returns an associative array of region codes => localized text labels
     * for a given country.
     */
    static function get_region_options($country_code)
    {
        $res = array();
        foreach (static::get_region_codes($country_code) as $region_code)
        {
            $res[$region_code] = __($region_code);
        }
        asort($res);
        return $res;
    }
}