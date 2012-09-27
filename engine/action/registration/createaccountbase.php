<?php

abstract class Action_Registration_CreateAccountBase extends Action
{
    protected $org = null;
    
    protected function get_login_url()
    {
        return '/pg/login';
    }
    
    protected function show_possible_duplicates($dups)
    {
        $this->page_draw(array(
            'title' => __("register:possible_duplicate"),
            'content' => view("org/possible_duplicate", array(
                'message' => __('register:possible_duplicate'), 
                'login_url' => $this->get_login_url(), 
                'duplicates' => $dups
            )),
            'org_only' => true,
        ));
    } 

    protected function post_process_input() {} // subclasses should override
    
    protected function get_country()
    {
        return null;
    }
    
    function process_input()
    {
        $name = trim(Input::get_string('org_name'));

        if (!$name)
        {
            throw new ValidationException(__('register:no_name'));
        }

        $username = trim(Input::get_string('username'));

        User::validate_username($username);

        $password = Input::get_string('password');
        $password2 = Input::get_string('password2');

        $email = EmailAddress::validate(trim(Input::get_string('email')));
        $phone_number = Input::get_string('phone');
        $city = Input::get_string('city');
        $region = Input::get_string('region');              
        $country = $this->get_country() ?: '';
        
        User::validate_password($password, $password2, array(
            $name, 
            $username, 
            $email, 
            $phone_number, 
            $city, 
            Geography::get_region_name($region) ?: '',
            Geography::get_country_name($country) ?: ''
        ));

        if (!Input::get_string('ignore_possible_duplicates'))
        {
            $dups = Organization::query()
                ->where("(username = ? OR (email = ? AND ? <> '') OR INSTR(name,?) > 0 OR INSTR(?,name) > 0)", 
                    $username, $email, $email, $name, $name)
                ->filter();  
                
            if (sizeof($dups) > 0)
            {
                return $this->show_possible_duplicates($dups);
            }
        }
        
        if (User::get_by_username($username, true))
        {
            throw new ValidationException(__('register:username_exists'));
        }                
        
        $org = new Organization();
        $org->set_defaults();
        
        $org->username = $username;
        $org->set_email($email);
        $org->name = $name;
        $org->set_password($password);        
        $org->city = $city;
        $org->region = $region;
        $org->country = $country;            
        $org->language = Language::get_current_code();
        $org->setup_state = User::SetupStarted;
        $org->set_design_setting('tagline', $org->get_location_text(false));
        $org->geocode_lat_long();
        
        // set phone number after country so canonicalization works
        $org->set_phone_number($phone_number);

        $org->save();

        $org->update_scope();
        $org->save();
        
        $org->init_default_widgets();
        
        Session::login($org);

        SessionMessages::add(__('register:created_ok'));   

        $this->org = $org;
        
        $this->post_process_input();
    }      
}