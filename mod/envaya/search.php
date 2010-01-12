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

    // Load Elgg engine
        require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
        
    // Set context
        set_context('search');
        
    // Get input
        $query = stripslashes(get_input('q'));        
        $title = sprintf(elgg_echo('searchtitle'),$query);         
        
        if (!empty($query)) {
            $body = "";
            
            $body.= "<div class='padded'><form method='GET' action='".$CONFIG->wwwroot."pg/org/search/'><input type='text' name='q' value='".$query."'><input type='submit' value='search'></form></div>";
            
            $body .= elgg_view_title($title); // elgg_view_title(sprintf(elgg_echo('searchtitle'),$tag));                        
            
            
            $latlong = elgg_geocode_location($query);
            
            $results = '';
            
            if ($latlong) 
            {     
                $radius = 2.0;
            
                $nearby = get_entities_in_area($latlong['lat'], $latlong['long'], $radius, 'group', 'organization', 0, "", 10, 0, false, $site_guid = 0);
            
                $results .= elgg_view("org/map", array('lat' => $latlong['lat'], 'long' => $latlong['long'], 'pin' => 'true', 'nearby' => $nearby, 'zoom' => '8'));
                
                //$results .= list_entities_in_area($latlong['lat'], $latlong['long'], 1.5, 'group', 'organization',0,10,false, false);
            }
            
            $results .= list_entities_from_metadata('', elgg_strtolower($query), 'group', 'organization', array(), 10, false, false);
            if ($results)
            {
                $body .= $results;
            }
            else
            {
                $body .= "<div class='padded'>No results found!</div>";
            }
            $body = elgg_view_layout('one_column',$body);
        }
        
        page_draw($title,$body);

?>