<?php

class Geocoder
{
    /**
     * Encode a location into a latitude and longitude, caching the result.
     *
     * @param String $location The location, e.g. "London", or "24 Foobar Street, Gotham City"
     */
    static function geocode($location)
    {
        global $CONFIG;

        // Handle cases where we are passed an array (shouldn't be but can happen if location is a tag field)
        if (is_array($location))
            $location = implode(', ', $location);

        $cached_location = get_data_row("SELECT * from geocode_cache WHERE location=?", array($location));
        if ($cached_location)
            return array('lat' => $cached_location->lat, 'long' => $cached_location->long);

        // Trigger geocode event if not cached
        $return = static::google_geocode($location);

        // If returned, cache and return value
        if ($return)
        {
            $lat = (float)$return['lat'];
            $long = (float)$return['long'];

            // Put into cache at the end of the page since we don't really care that much
            execute_delayed_write_query("INSERT DELAYED INTO geocode_cache (location, lat, `long`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE lat=?, `long`=?",
                array($location, $lat, $long, $lat, $long)
            );
        }

        return $return;
    }
    
    /**
     * Mobworking.net geocoder
     *
     * @author Marcus Povey <marcus@dushka.co.uk>
     * @copyright Marcus Povey 2008-2009
     */
    static function google_geocode($location)
    {
        global $CONFIG;
        $google_api = $CONFIG->google_api_key;

        // Desired address
        $address = "http://maps.google.com/maps/geo?q=".urlencode($location)."&output=json&key=" . $google_api;

        // Retrieve the URL contents
        $result = file_get_contents($address);
        $obj = json_decode($result);

        $obj = @$obj->Placemark[0]->Point->coordinates;

        if ($obj)
        {
            return array('lat' => $obj[1], 'long' => $obj[0]);
        }
        return null;
    }
}