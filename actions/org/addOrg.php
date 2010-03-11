<?php
    
    global $CONFIG;
    
    action_gatekeeper();

    $username = trim(get_input('username'));
    $password = get_input('password');
    $password2 = get_input('password2');
    $email = trim(get_input('email'));
    $name = trim(get_input('name'));
    $language = get_input('language');
        
    try 
    {
        if (strcmp($password, $password2) != 0)
        {
            throw new RegistrationException(elgg_echo('user:password:fail:notsame'));
        }
        
        if (!validate_email_address($email)) 
        {
            throw new RegistrationException(elgg_echo('registration:emailnotvalid'));
        }    
                    
        if (!validate_password($password)) 
        {    
            throw new RegistrationException(elgg_echo('registration:passwordnotvalid'));
        }    
        
        if (!validate_username($username)) 
        {
            throw new RegistrationException(elgg_echo('registration:usernamenotvalid'));
        }    
        
        access_show_hidden_entities(true);
        
        if (get_user_by_username($username)) 
        {
            throw new RegistrationException(elgg_echo('registration:userexists'));
        }

        $org = new Organization();
        $org->username = $username;
        $org->email = $email;
        $org->name = $name;
        $org->access_id = ACCESS_PUBLIC;
        $org->salt = generate_random_cleartext_password(); 
        $org->password = generate_user_password($org, $password); 
        $org->owner_guid = 0; 
        $org->container_guid = 0;                 
        $org->language = $language;
        
        $org->save();        

        $guid = $org->guid;

        login($org, false);

        system_message(sprintf(elgg_echo("registerok"),$CONFIG->sitename));

        forward($org->getURL() . "/edit"); 
    } 
    catch (RegistrationException $r) 
    {    
        $_SESSION['input'] = $_POST;
        
        register_error($r->getMessage());
        forward("org/new"); 
    }
    
?>    