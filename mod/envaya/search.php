<?php

    /**
     * Generic search viewer
     * Given a GUID, this page will try and display any entity
     *
     * @package Elgg
     * @subpackage Core

     * @author Curverider Ltd

     * @link http://elgg.org/
     */

	    // Set context
        set_context('search');

	    // Get input
    	$lat = get_input('lat');
    	$long = get_input('long');

    	if ($lat && $long)
    	{
			$latlong = array('lat' => $lat, 'long' => $long);
            
            $title = sprintf(elgg_echo('browse_map'));
    	}
    	else
    	{
	        $query = stripslashes(get_input('q'));
	        $title = sprintf(elgg_echo('searchtitle'),$query);

			if (!empty($query)) {
				$body = "";

				$body.= "<div class='padded'><form method='GET' action='".$CONFIG->wwwroot."pg/org/search/'><input type='text' name='q' value='".$query."'><input type='submit' value='".elgg_echo('search')."'></form></div>";

				$latlong = elgg_geocode_location($query);
        	}
	    }

		$results = '';

		if ($latlong)
		{
			$radius = 2.0;

			$nearby = get_entities_in_area($latlong['lat'], $latlong['long'], $radius, 'user', 'organization', 0, "", 10, 0, false, $site_guid = 0);

			if ($nearby)
			{
				$results .= "<div class='padded'>".elgg_view("org/map", array('lat' => $latlong['lat'], 'long' => $latlong['long'], 'pin' => 'true', 'nearby' => $nearby, 'zoom' => '8'))."</div>";
			}

			//$results .= list_entities_in_area($latlong['lat'], $latlong['long'], 1.5, 'group', 'organization',0,10,false, false);
		}

		if (!empty($query))
		{
			$results .= list_entities_from_metadata('', elgg_strtolower($query), 'user', 'organization', array(), 10, false, false);
		}

		if ($results)
		{
			$body .= $results;
		}
		else
		{
			$body .= "<div class='padded'>" . elgg_echo("org:searchnoresults") . "</div>";
		}

		$body = elgg_view_layout('one_column',elgg_view_title($title), $body);

        page_draw($title,$body);
?>