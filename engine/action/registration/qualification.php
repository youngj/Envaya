<?php

class Action_Registration_Qualification extends Action
{
    function process_input()
    {            
        $country = get_input('country');

        if (!Geography::is_supported_country($country))
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
        $this->redirect(secure_url("/org/new?step=2"));            
    }
}
