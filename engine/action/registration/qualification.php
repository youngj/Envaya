<?php

class Action_Registration_Qualification extends Action
{
    function process_input()
    {            
        $approvedCountries = array('tz');

        try
        {
            $country = get_input('country');

            if (!in_array($country, $approvedCountries))
            {
                throw new RegistrationException(__("qualify:wrong_country"));
            }

            $orgType = get_input('org_type');
            if ($orgType != 'np')
            {
                throw new RegistrationException(__("qualify:wrong_org_type"));
            }

            Session::set('registration', array(
                'country' => get_input('country'),
            ));

            system_message(__("qualify:ok"));
            forward(Config::get('secure_url')."org/new?step=2");            

        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }
    }
}