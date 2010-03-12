<?php

    gatekeeper();    
    action_gatekeeper();
        
    try 
    {
        $org = get_loggedin_user();
        $org->mission = get_input('mission');
        $org->language = get_input('content_language');
        $org->email_public = get_input('email_public');
        $org->setSectors(get_input_array('sector'));
        $org->city = get_input('city');
        $org->region = get_input('region');
        $org->sector_other = get_input('sector_other');

        $latlong = elgg_geocode_location($org->getLocationText());
    
        if ($latlong)
        {
            $org->setLatLong($latlong['lat'], $latlong['long']);
        }            
        
        $org->setup_state = 5;
        $org->save();        
                       
        system_message(elgg_echo("setup:ok"));

        forward($org->getUrl());
    } 
    catch (RegistrationException $r) 
    {    
        $_SESSION['input'] = $_POST;
        
        register_error($r->getMessage());
        forward_to_referrer();
    }
    
?>    