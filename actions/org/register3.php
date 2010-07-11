<?php

    gatekeeper();
    action_gatekeeper();

    try
    {
        $org = get_loggedin_user();

        $mission = get_input('mission');
        if (!$mission)
        {
            throw new RegistrationException(elgg_echo("setup:mission:blank"));
        }

        $sectors = get_input_array('sector');
        if (sizeof($sectors) == 0)
        {
            throw new RegistrationException(elgg_echo("setup:sector:blank"));
        }
        else if (sizeof($sectors) > 5)
        {
            throw new RegistrationException(elgg_echo("setup:sector:toomany"));
        }

        $homeWidget = $org->getWidgetByName('home');
        $homeWidget->setContent($mission, false);

        $org->language = get_input('content_language');

        $org->setSectors($sectors);
        $org->city = get_input('city');
        $org->region = get_input('region');
        $org->sector_other = get_input('sector_other');

        $org->theme = get_input('theme');

        $latlong = elgg_geocode_location($org->getLocationText());

        if ($latlong)
        {
            $org->setLatLong($latlong['lat'], $latlong['long']);
        }

        $homeWidget->save();

        $org->setup_state = 5;
        $org->save();

        system_message(elgg_echo("setup:ok"));

        trigger_elgg_event('register', 'organization', $org);

        forward($org->getUrl());
    }
    catch (RegistrationException $r)
    {
        action_error($r->getMessage());
    }

?>