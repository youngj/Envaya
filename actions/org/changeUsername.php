<?php

    admin_gatekeeper();
    action_gatekeeper();
    
    $org = get_entity(get_input('guid'));
    
    if ($org)
    {
        $username = get_input('username');
    
        if ($username && $username != $org->username)
        {
            try 
            {
                validate_username($username);
            }
            catch (RegistrationException $ex)
            {
                register_error($ex->getMessage());
                forward_to_referrer();
            }            
            
            if (get_user_by_username($username))
            {
                register_error(elgg_echo('registration:userexists'));
                forward_to_referrer();
            }
        
            $org->username = $username;
            $org->save();

            system_message(elgg_echo('username:changed'));
        }    
        forward($org->getURL());
    }