<?php
	/**
	 * Mobworking.net geocoder
	 * 
	 * @author Marcus Povey <marcus@dushka.co.uk>
	 * @copyright Marcus Povey 2008-2009
	 */


	function googlegeocoder_init()
	{
		// Register geocoder hook
		register_plugin_hook('geocode', 'location', 'googlegeocoder_geocode');
		
		// Listen to create events on a low priority
		register_elgg_event_handler('create','all','googlegeocoder_tagger', 1000);
	}
	
	/** 
	 * Google geocoder.
	 *
	 * Listen for an Elgg Geocode request and use google maps to geocode it.
	 */
	function googlegeocoder_geocode($hook, $entity_type, $returnvalue, $params)
	{ 
		if (isset($params['location']))
		{
            $google_api = get_plugin_setting('google_api', 'googlegeocoder'); // ABQIAAAAHy69XWEjciJIVElz0OYMsRR3-IOatrPZ1tLat998tYHgwqPnkhTKyWcq8ytRPMx3RyxFjK0O7WSCHA
		
			// Desired address
		   	$address = "http://maps.google.com/maps/geo?q=".urlencode($params['location'])."&output=json&key=" . $google_api;
		
		   	// Retrieve the URL contents
	   		$result = file_get_contents($address);
	   		$obj = json_decode($result);
	   		
	   		$obj = $obj->Placemark[0]->Point->coordinates;
            
            if ($obj)
            {   		
	   		    return array('lat' => $obj[1], 'long' => $obj[0]);
            }
		}
	}
	
	/**
	 * Listen to the create events of new Locatable things and tag
	 * them with a location (if possible).
	 */ 
	function googlegeocoder_tagger($event, $object_type, $object)
	{
		if ($object instanceof Locatable)
		{
        
			$location = false;
		
			// See if object has a specific location
			if (isset($object->location))
				$location = $object->location;
				
			// If not, see if user has a location
			if (!$location) {
				if (isset($object->owner_guid))
				{
					$user = get_entity($object->owner_guid);
					if (isset($user->location)) $location = $user->location;
				}
			}
			
			// Nope, so use logged in user
			if (!$location) {
				$user = get_loggedin_user();
				if (($user) && (isset($user->location)))
					$location = $user->location;
			}
			
			// Have we got a location
			if ($location)
			{
				// Handle when location is given in a tag field (as it is with users)
				if (is_array($location))
					$location = implode(', ', $location);
				
				$latlong = elgg_geocode_location($location);
				
				if ($latlong)
				{
					$object->setLatLong($latlong['lat'], $latlong['long']);
					$object->setLocation($location);
				}
			}
		}
		
	}
	

	// Initialisation
	register_elgg_event_handler('init','system','googlegeocoder_init');
?>
