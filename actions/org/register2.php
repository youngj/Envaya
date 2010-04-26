<?php
    
    action_gatekeeper();
        
    try 
    {
        $name = trim(get_input('org_name'));
        
        if (!$name)
        {
            throw new RegistrationException(elgg_echo('create:no_name'));
        }
    
        $username = trim(get_input('username'));
        
        validate_username($username);
    
        access_show_hidden_entities(true);
        
        if (get_user_by_username($username)) 
        {
            throw new RegistrationException(elgg_echo('create:username_exists'));
        }
    
    
        $password = get_input('password');
        $password2 = get_input('password2');
    
        if (strcmp($password, $password2) != 0)
        {
            throw new RegistrationException(elgg_echo('create:passwords_differ'));
        }
        
        validate_password($password);
        
        $email = trim(get_input('email'));
        
        validate_email_address($email);
                    
        $org = new Organization();
        $org->username = $username;
        $org->email = $email;
        $org->name = $name;
        $org->access_id = ACCESS_PUBLIC;
        $org->salt = generate_random_cleartext_password(); 
        $org->password = generate_user_password($org, $password); 
        $org->owner_guid = 0; 
        $org->container_guid = 0;                 
        $org->language = get_language();
        $org->theme = "green";
        $org->setup_state = 3; 
                        
        $prevInfo = Session::get('registration');        

        //$org->registration_number = $prevInfo['registration_number'];
        $org->country = $prevInfo['country'];
        $org->local = $prevInfo['local']; 
        
        $org->setLatLong(-6.140555,35.551758);            
        
        $org->save();       
        
        /* auto-create empty pages */
        $org->getWidgetByName('news')->save();
        $org->getWidgetByName('team')->save();
        $org->getWidgetByName('projects')->save();
        $org->getWidgetByName('history')->save();
        $org->getWidgetByName('partnerships')->save();
        
        $contactWidget = $org->getWidgetByName('contact');
        $contactWidget->public_email = "yes";
        $contactWidget->save();        

        $guid = $org->guid;

        login($org, false);

        Session::set('registration', null);

        system_message(sprintf(elgg_echo("create:ok"),$CONFIG->sitename));

        forward("org/new?step=3");
    } 
    catch (RegistrationException $r) 
    {    
        action_error($r->getMessage());
    }
    
?>    