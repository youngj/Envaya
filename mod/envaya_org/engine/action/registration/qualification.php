<?php

class Action_Registration_Qualification extends Action
{    
    function before()
    {
        Permission_Public::require_any();

        $invite_code = Input::get_string('invite');
    
        if (Session::is_logged_in())
        {
            throw new RedirectException('', "/pg/register_logged_in?next=/org/new");
        }
        
        if ($invite_code)
        {
            Session::set('invite_code', $invite_code);
        }       
    }

    function render()
    {        
        $this->allow_view_types(null);        
        
        $testing_country = Input::get_string('testing_country');
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
        $country = Input::get_string('country');

        if (!Geography::is_available_country($country))
        {
            throw new ValidationException(__("register:wrong_country"));
        }

        $orgType = Input::get_string('org_type');
        if ($orgType != 'np')
        {
            throw new ValidationException(__("register:wrong_org_type"));
        }

        Session::set('registration', array(
            'country' => Input::get_string('country'),
        ));

        SessionMessages::add(__("register:qualify_ok"));
        $this->redirect(secure_url("/org/create_account"));            
    }
}
