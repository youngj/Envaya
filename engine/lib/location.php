<?php

    /**
     * Elgg geo-location tagging library.
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     */

    /**
     * Define an interface for geo-tagging entities.
     *
     */
    interface Locatable
    {
        /** Set a location text */
        public function setLocation($location);

        /**
         * Set latitude and longitude tags for a given entity.
         *
         * @param float $lat
         * @param float $long
         */
        public function setLatLong($lat, $long);

        /**
         * Get the contents of the ->geo:lat field.
         *
         */
        public function getLatitude();

        /**
         * Get the contents of the ->geo:lat field.
         *
         */
        public function getLongitude();

        /**
         * Get the ->location metadata.
         *
         */
        public function getLocation();
    }

    /**
     * Encode a location into a latitude and longitude, caching the result.
     *
     * Works by triggering the 'geocode' 'location' plugin hook, and requires a geocoding module to be installed
     * activated in order to work.
     *
     * @param String $location The location, e.g. "London", or "24 Foobar Street, Gotham City"
     */
    function elgg_geocode_location($location)
    {
        global $CONFIG;

        // Handle cases where we are passed an array (shouldn't be but can happen if location is a tag field)
        if (is_array($location))
            $location = implode(', ', $location);

        $cached_location = get_data_row("SELECT * from geocode_cache WHERE location=?", array($location));
        if ($cached_location)
            return array('lat' => $cached_location->lat, 'long' => $cached_location->long);

        // Trigger geocode event if not cached
        $return = false;
        $return = trigger_plugin_hook('geocode', 'location', array('location' => $location), $return);

        // If returned, cache and return value
        if (($return) && (is_array($return)))
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

    // Some distances in degrees (approximate)
    define("MILE", 0.01515);
    define("KILOMETER", 0.00932);