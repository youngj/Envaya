<?php

abstract class Action_Registration_CreateAccountBase extends Action
{
    protected $org = null;
    
    protected function get_login_url()
    {
        return '/pg/login';
    }
    
    protected function show_possible_duplicate($ex)
    {
        $this->page_draw(array(
            'title' => __("create:possible_duplicate"),
            'content' => view("org/possible_duplicate", array(
                'message' => $ex->getMessage(), 
                'login_url' => $this->get_login_url(), 
                'duplicates' => $ex->duplicates
            )),
            'org_only' => true,
        ));
    } 

    protected function _process_input()
    {        
        $name = trim(get_input('org_name'));

        if (!$name)
        {
            throw new ValidationException(__('create:no_name'));
        }

        $username = trim(get_input('username'));

        validate_username($username);

        $password = get_input('password');
        $password2 = get_input('password2');

        if (strcmp($password, $password2) != 0)
        {
            throw new ValidationException(__('create:passwords_differ'));
        }

        $lpassword = strtolower($password);
        $lusername = strtolower($username);
        $lname = strtolower($name);

        if (strpos($lname, $lpassword) !== FALSE || strpos($lusername, $lpassword) !== FALSE)
        {
            throw new ValidationException(__('create:password_too_easy'));
        }

        validate_password($password);

        $email = validate_email_address(trim(get_input('email')));

        if (!get_input('ignore_possible_duplicates'))
        {
            $dups = Organization::query()
                ->where("(username = ? OR (email = ? AND ? <> '') OR INSTR(name,?) > 0 OR INSTR(?,name) > 0)", 
                    $username, $email, $email, $name, $name)
                ->filter();  
                
            if (sizeof($dups) > 0)
            {
                throw new PossibleDuplicateException(__('create:possible_duplicate'), $dups);
            }
        }
        
        if (get_user_by_username($username))
        {
            throw new ValidationException(__('create:username_exists'));
        }                
        
        $org = new Organization();
        $org->username = $username;
        $org->set_phone_number(get_input('phone'));
        $org->email = $email;
        $org->name = $name;
        $org->set_password($password);
        $org->owner_guid = 0;
        $org->container_guid = 0;
        $org->language = Language::get_current_code();
        $org->theme = "green";
        $org->setup_state = SetupState::CreatedAccount;

        //$org->registration_number = $prevInfo['registration_number'];
        //$org->local = $prevInfo['local'];

        $org->set_lat_long(-6.140555,35.551758);

        $org->save();

        /* auto-create empty pages */
        $org->get_widget_by_name('home')->save();
        
        $org->get_widget_by_name('news')->save();

        $contactWidget = $org->get_widget_by_name('contact');
        if ($email)
        {
            $contactWidget->public_email = "yes";
        }
        $contactWidget->save();

        $guid = $org->guid;
        
        login($org, false);

        SessionMessages::add(__("create:ok"));   

        $this->org = $org;
    }
}