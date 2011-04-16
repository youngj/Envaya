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
                throw new ValidationException(__("qualify:wrong_country"));
            }

            $orgType = get_input('org_type');
            if ($orgType != 'np')
            {
                throw new ValidationException(__("qualify:wrong_org_type"));
            }

            Session::set('registration', array(
                'country' => get_input('country'),
            ));

            SessionMessages::add(__("qualify:ok"));
            forward(Config::get('secure_url')."org/new?step=2");            

        }
        catch (ValidationException $r)
        {
            redirect_back_error($r->getMessage());
        }
    }
}