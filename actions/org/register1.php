<?php

    $approvedCountries = array('tz');

    try
    {
        $country = get_input('country');

        if (!in_array($country, $approvedCountries))
        {
            throw new RegistrationException(elgg_echo("qualify:wrong_country"));
        }

        $orgType = get_input('org_type');
        if ($orgType != 'np')
        {
            throw new RegistrationException(elgg_echo("qualify:wrong_org_type"));
        }

        Session::set('registration', array(
            //'registration_number' => get_input('registration_number'),
            'country' => get_input('country'),
        ));

        system_message(elgg_echo("qualify:ok"));
        forward("org/new?step=2");

    }
    catch (RegistrationException $r)
    {
        action_error($r->getMessage());
    }
