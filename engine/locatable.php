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
     */
    interface Locatable
    {

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
