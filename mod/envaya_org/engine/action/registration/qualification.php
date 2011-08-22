<?php

class Action_Registration_Qualification extends Action
{    
    function before()
    {
        $invite_code = get_input('invite');
    
        if (Session::isloggedin())
        {
            throw new RedirectException('', "/org/register_logged_in");
        }
        
        if ($invite_code)
        {
            Session::set('invite_code', $invite_code);
        }
        
    }

    function render()
    {        
        $this->allow_view_types(null);        
        
        $testing_country = get_input('testing_country');
        if (!empty($testing_country) && !Geography::is_available_country($testing_country))
        {
            throw new ValidationException(__("register:wrong_country"));
        }        
        
        $this->page_draw(array(
            'title' => __("register:title"),
            'content' => view("org/qualification", array('testing_country' => $testing_country)),
            'org_only' => true
        ));
    }    

    function process_input()
    {            
        $country = get_input('country');

        if (!Geography::is_available_country($country))
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
        $this->redirect(secure_url("/org/create_account"));            
    }
}
