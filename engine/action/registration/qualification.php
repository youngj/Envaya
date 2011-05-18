<?php

class Action_Registration_Qualification extends Action
{
    function process_input()
    {            
        $approvedCountries = Geography::get_approved_countries();

        $country = get_input('country');

        if (!in_array($country, $approvedCountries))
        {
            throw new ValidationException(__("register:wrong_country"));
        }

        $orgType = get_input('org_type');
        if ($orgType != 'np')
        {
            throw new ValidationException(__("register:wrong_org_type"));
        }

        Session::set('registration', array(
            'country' => get_input('country'),
        ));

        SessionMessages::add(__("register:qualify_ok"));
        $this->redirect(Config::get('secure_url')."org/new?step=2");            
    }
}
