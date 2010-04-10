<?php

    /**
	 * Elgg actions
	 * Allows system modules to specify actions
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @author Curverider Ltd
	 * @link http://elgg.org/
	 */
     
    // Action setting and run *************************************************
    
   	/**
   	 * Loads an action script, if it exists, then forwards elsewhere
   	 *
   	 * @param string $action The requested action
   	 * @param string $forwarder Optionally, the location to forward to
   	 */
    
        function action($action, $forwarder = "pg/home") {
            
            global $CONFIG;
            
            $userId = get_loggedin_userid();
            if ($userId) 
            {
                set_last_action($userId);
            }    
            
	        $query = parse_url($_SERVER['REQUEST_URI']);
			if (isset($query['query'])) {
				$query = $query['query'];
				$query = rawurldecode($query);
				$query = explode('&',$query);
				if (sizeof($query) > 0) {
					foreach($query as $queryelement) {
						$vals = explode('=',$queryelement, 2);
						if (sizeof($vals) > 1) {
							set_input(trim($vals[0]),trim($vals[1]));
						}
					}
				}
			}
            
            $forwarder = str_replace($CONFIG->url, "", $forwarder);
            $forwarder = str_replace("http://", "", $forwarder);
            $forwarder = str_replace("@", "", $forwarder);

            if (substr($forwarder,0,1) == "/") {
                $forwarder = substr($forwarder,1);
            }
            
            if (isset($CONFIG->actions[$action]))
            {
                $file = $CONFIG->actions[$action]['file'];
            }
            else
            {
                $file = get_default_action_path($action);
            }
            
            if (include($file))
            {   
                // ok
            } 
            else 
            {
                register_error(sprintf(elgg_echo('actionundefined'),$action));
            }
            
            forward($CONFIG->url . $forwarder);
            
        }
        
        function get_default_action_path($action)
        {
            global $CONFIG;
            return $CONFIG->path . "actions/" . $action . ".php";        
        }
    
	/**
	 * Registers a particular action in memory
	 *
	 * @param string $action The name of the action (eg "register", "account/settings/save")
	 * @param string $filename Optionally, the filename where this action is located
	 */
        
        function register_action($action, $filename = "") 
        {
            global $CONFIG;            
            
            if (!isset($CONFIG->actions)) {
                $CONFIG->actions = array();
            }
            
            if (empty($filename)) 
            {
            	$filename = get_default_action_path($action);
            }
            
            $CONFIG->actions[$action] = array('file' => $filename);
            return true;
        }

	/**
	 * Actions to perform on initialisation
	 *
	 * @param string $event Events API required parameters
	 * @param string $object_type Events API required parameters
	 * @param string $object Events API required parameters
	 */
        
        function actions_init($event, $object_type, $object) {
        	return true;
        }
        
        /**
         * Validate an action token, returning true if valid and false if not
         *
         * @return unknown
         */
        function validate_action_token($visibleerrors = true)
        {
        	$token = get_input('__elgg_token');
        	$ts = get_input('__elgg_ts');
        	$session_id = Session::id();
            
        	if (($token) && ($ts) && ($session_id))
        	{
	        	// generate token, check with input and forward if invalid
	        	$generated_token = generate_action_token($ts);
	        	
	        	// Validate token
	        	if (strcmp($token, $generated_token)==0)
	        	{
	        		$hour = 60*60*24;
	        		$now = time();
	        		
	        		// Validate time to ensure its not crazy
	        		if (($ts>$now-$hour) && ($ts<$now+$hour))
	        		{
	        			return true; 	        			
	        		}
	        		else if ($visibleerrors)
	        			register_error(elgg_echo('actiongatekeeper:timeerror'));
	        	}
	        	else if ($visibleerrors)
                {                    
                    register_error(elgg_echo('actiongatekeeper:timeerror'));
                }    
        	}
        	else if ($visibleerrors)
            {
        		register_error(elgg_echo('actiongatekeeper:missingfields'));
            }    
        		
        	return false;
        }

       	/**
       	 * Action gatekeeper.
       	 * This function verifies form input for security features (like a generated token), and forwards
       	 * the page if they are invalid.
       	 * 
       	 * Place at the head of actions.
       	 */
        function action_gatekeeper()
        {
        	if (validate_action_token())
        		return true;
        		
        	forward();
        	exit;
        }
        
        function action_error($msg)
        {
            register_error($msg);
            Session::saveInput();
            forward_to_referrer();
        }
        
        /**
         * Generate a token for the current user suitable for being placed in a hidden field in action forms.
         * 
         * @param int $timestamp Unix timestamp
         */
        function generate_action_token($timestamp)
        {
        	// Get input values
        	$site_secret = get_site_secret();
        	
        	// Current session id
        	$session_id = session_id();
        	
        	// Get user agent
        	$ua = $_SERVER['HTTP_USER_AGENT'];
        	
        	// Session token
        	$st = Session::get('__elgg_session');
        	
        	if (($site_secret) && ($session_id))
        		return md5($site_secret.$timestamp.$session_id.$ua.$st);
        	
        	return false;
        }
       
        /**
         * Initialise the site secret.
         *
         */
        function init_site_secret()
        {
        	$secret = md5(rand().microtime());
        	if (datalist_set('__site_secret__', $secret))
        		return $secret;
        		
        	return false;
        }
        
        /**
         * Retrieve the site secret.
         *
         */
        function get_site_secret()
        {
        	$secret = datalist_get('__site_secret__');
        	if (!$secret) $secret = init_site_secret();
        	
        	return $secret;
        }
        
    // Register some actions ***************************************************
    
        register_elgg_event_handler("init","system","actions_init");

?>