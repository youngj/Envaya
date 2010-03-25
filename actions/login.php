<?php

    /**
	 * Elgg login action
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */
	 
    // Get username and password
    
        $username = get_input('username');
        $password = get_input("password");
        $persistent = get_input("persistent", false);
        
    // If all is present and correct, try to log in  
    	$result = false;          
        if (!empty($username) && !empty($password)) {
        	if ($user = authenticate($username,$password)) {
        		$result = login($user, $persistent);
        	}
        }
        
    // Set the system_message as appropriate
        
        if ($result) {
            system_message(sprintf(elgg_echo('loginok'), $user->name));
            
            $forward_url = Session::get('last_forward_from');
            if ($forward_url)
            {
            	Session::set('last_forward_from', null);
            	forward($forward_url);
            }
            else
            {
                if (get_input('returntoreferer')) 
                {
            		forward($_SERVER['HTTP_REFERER']);
            	} 
                else if (!$user->isSetupComplete())
                {
                    forward("org/new?step={$user->setup_state}");
                }
                else
                {
            		forward("pg/dashboard/");
                }    
            }
        } else {
        	$error_msg = elgg_echo('loginerror');
        	// figure out why the login failed
        	if (!empty($username) && !empty($password)) {
        		// See if it exists and is disabled
				$access_status = access_get_show_hidden_status();
				access_show_hidden_entities(true);
                
                register_error(elgg_echo('loginerror'));               
                forward("pg/login");
        		
        		access_show_hidden_entities($access_status);
        	} else {
            	register_error(elgg_echo('loginerror'));
        	}
        }
      
?>