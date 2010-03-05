<?php

	/**
	 * Elgg session management
	 * Functions to manage logins
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

	/** Elgg magic session */
	global $SESSION;

	/**
	 * Magic session class.
	 * This class is intended to extend the $_SESSION magic variable by providing an API hook
	 * to plug in other values.
	 *
	 * Primarily this is intended to provide a way of supplying "logged in user" details without touching the session 
	 * (which can cause problems when accessed server side).
	 * 
	 * If a value is present in the session then that value is returned, otherwise a plugin hook 'session:get', '$var' is called,
	 * where $var is the variable being requested.
	 * 
	 * Setting values will store variables in the session in the normal way.
	 * 
	 * LIMITATIONS: You can not access multidimensional arrays
	 * 
	 * This is EXPERIMENTAL.
	 */
	class ElggSession implements ArrayAccess
	{
		/** Local cache of trigger retrieved variables */
		private static $__localcache; 
		
		function __isset($key) { return $this->offsetExists($key); }
				
		/** Set a value, go straight to session. */
		function offsetSet($key, $value) { $_SESSION[$key] = $value; } 
 		
		/**
		 * Get a variable from either the session, or if its not in the session attempt to get it from
		 * an api call.
		 */
 		function offsetGet($key) 
 		{ 
 			if (!ElggSession::$__localcache)
 				ElggSession::$__localcache = array();
 				
 			if (isset($_SESSION[$key]))
 				return $_SESSION[$key];
 				
 			if (isset(ElggSession::$__localcache[$key]))
 				return ElggSession::$__localcache[$key];
 			
 			$value = null;
 			$value = trigger_plugin_hook('session:get', $key, null, $value);
 			
 			ElggSession::$__localcache[$key] = $value;
 			
   			return ElggSession::$__localcache[$key];
 		} 
 		
 		/**
 		 * Unset a value from the cache and the session.
 		 */
 		function offsetUnset($key) 
 		{
   			unset(ElggSession::$__localcache[$key]);
			unset($_SESSION[$key]); 
 		} 
 		
 		/**
 		 * Return whether the value is set in either the session or the cache.
 		 */
 		function offsetExists($offset) { 
			if (isset(ElggSession::$__localcache[$offset]))
				return true;
				
			if (isset($_SESSION[$offset]))
				return true;

			if ($this->offsetGet($offset)) return true;
		}
	}
	
		
	/**
	 * Return the current logged in user, or null if no user is logged in.
	 *
	 * If no user can be found in the current session, a plugin hook - 'session:get' 'user' to give plugin 
	 * authors another way to provide user details to the ACL system without touching the session.
	 */
		function get_loggedin_user()
		{
			global $SESSION;
		
			if (isset($SESSION))
				return $SESSION['user'];
				
			return false;
		}
		
	/**
	 * Return the current logged in user by id.
	 * 
	 * @see get_loggedin_user()
	 * @return int
	 */
		function get_loggedin_userid()
		{
			$user = get_loggedin_user();
			if ($user)
				return $user->guid;
				
			return 0;
		}

	/**
	 * Returns whether or not the user is currently logged in
	 *
	 * @return true|false
	 */
		function isloggedin() {
						
			if (!is_installed()) return false; 
			
			$user = get_loggedin_user();
		
			if ((isset($user)) && ($user instanceof ElggUser) && ($user->guid > 0))
				return true;
				
			return false;
			
		}

	/**
	 * Returns whether or not the user is currently logged in and that they are an admin user.
	 *
	 * @uses isloggedin()
	 * @return true|false
	 */
		function isadminloggedin()
		{
			if (!is_installed()) return false; 
			
			$user = get_loggedin_user();
			
			if (isloggedin() && $user->admin)
				return true;
				
			return false;
		}
		
	/**
	 * Perform standard authentication with a given username and password.
	 * Returns an ElggUser object for use with login.
	 *
	 * @see login
	 * @param string $username The username, optionally (for standard logins)
	 * @param string $password The password, optionally (for standard logins)
	 * @return ElggUser|false The authenticated user object, or false on failure.
	 */
		
		function authenticate($username, $password) {
            
			if (pam_authenticate(array('username' => $username, 'password' => $password)))
            {
				return get_user_by_username($username);
            }    
            
            return false;
			
		}
		
		/**
		 * Hook into the PAM system which accepts a username and password and attempts to authenticate
		 * it against a known user.
		 *
		 * @param array $credentials Associated array of credentials passed to pam_authenticate. This function expects
		 * 		'username' and 'password' (cleartext).
		 */
		function pam_auth_userpass($credentials = NULL)
		{
			if (is_array($credentials) && ($credentials['username']) && ($credentials['password']))
			{
				
	            if ($user = get_user_by_username($credentials['username'])) {
	          	
					 // User has been banned, so bin them.
					 if ($user->isBanned()) return false;
						
	                 if ($user->password == generate_user_password($user, $credentials['password'])) 
	                 	
	                 	return true;
	                 else 
	                 	// Password failed, log.
	                 	log_login_failure($user->guid);
	                 	
	            }
			}
			
			return false;
		}
		
		function log_login_failure($user_guid)
		{
			$user_guid = (int)$user_guid;
			$user = get_entity($user_guid);
			
			if (($user_guid) && ($user) && ($user instanceof ElggUser))
			{
				$fails = (int)$user->getPrivateSetting("login_failures");
				$fails++;
				
				$user->setPrivateSetting("login_failures", $fails);
				$user->setPrivateSetting("login_failure_$fails", time());
			}
		}
		
		function reset_login_failure_count($user_guid)
		{
			$user_guid = (int)$user_guid;
			$user = get_entity($user_guid);
			
			if (($user_guid) && ($user) && ($user instanceof ElggUser))
			{
				$fails = (int)$user->getPrivateSetting("login_failures");
				
				if ($fails) {
					for ($n=1; $n <= $fails; $n++) 
						$user->removePrivateSetting("login_failure_$n");
						
					$user->removePrivateSetting("login_failures");
				}
			}
		}
		
		function check_rate_limit_exceeded($user_guid)
		{
			$limit = 5;
			$user_guid = (int)$user_guid;
			$user = get_entity($user_guid);
			
			if (($user_guid) && ($user) && ($user instanceof ElggUser))
			{
				$fails = (int)$user->getPrivateSetting("login_failures");
				if ($fails >= $limit)
				{
					$cnt = 0;
					$time = time();
					for ($n=$fails; $n>0; $n--)
					{
						$f = $user->getPrivateSetting("login_failure_$n");
						if ($f > $time - (60*5))
							$cnt++;
							
						if ($cnt==$limit) return true; // Limit reached
					}
				}
				
			}
			
			return false;
		}
		
	/**
	 * Logs in a specified ElggUser. For standard registration, use in conjunction
	 * with authenticate.
	 * 
	 * @see authenticate
	 * @param ElggUser $user A valid Elgg user object
	 * @param boolean $persistent Should this be a persistent login?
	 * @return true|false Whether login was successful
	 */
		function login(ElggUser $user, $persistent = false) {
            
            global $CONFIG;
            
            if ($user->isBanned()) return false; // User is banned, return false.
            //if (check_rate_limit_exceeded($user->guid)) return false; // Check rate limit
          
            $_SESSION['user'] = $user;
            $_SESSION['guid'] = $user->getGUID();
            $_SESSION['id'] = $_SESSION['guid'];
            $_SESSION['username'] = $user->username;
            $_SESSION['name'] = $user->name;
                     
            $code = (md5($user->name . $user->username . time() . rand()));

            $user->code = md5($code);
            
            $_SESSION['code'] = $code;
            
            if (($persistent))
				setcookie("elggperm", $code, (time()+(86400 * 30)),"/");
         
            if (!$user->save() || !trigger_elgg_event('login','user',$user)) {
            	unset($_SESSION['username']);
	            unset($_SESSION['name']);
	            unset($_SESSION['code']);
	            unset($_SESSION['guid']);
	            unset($_SESSION['id']);
	            unset($_SESSION['user']);
	            setcookie("elggperm", "", (time()-(86400 * 30)),"/");
            	return false;
            }
            
            // Users privilege has been elevated, so change the session id (help prevent session hijacking)
	        session_regenerate_id(); 

	        // Update statistics
	        set_last_login($_SESSION['guid']);
	        reset_login_failure_count($user->guid); // Reset any previous failed login attempts
	        
	        // Set admin shortcut flag if this is an admin
			if (isadminloggedin()) {
				global $is_admin;
				$is_admin = true;
			}
	        
			return true;
				
		}
        
	/**
	 * Log the current user out
	 *
	 * @return true|false
	 */
		function logout() {
            global $CONFIG;

            if (isset($_SESSION['user'])) {
            	if (!trigger_elgg_event('logout','user',$_SESSION['user'])) return false;
            	$_SESSION['user']->code = "";
            	$_SESSION['user']->save();
            }
            
            unset($_SESSION['username']);
            unset($_SESSION['name']);
            unset($_SESSION['code']);
            unset($_SESSION['guid']);
            unset($_SESSION['id']);
            unset($_SESSION['user']);
            
            setcookie("elggperm", "", (time()-(86400 * 30)),"/");
            
            session_destroy();
            
            return true;
        }
        
        function get_session_fingerprint()
        {
        	global $CONFIG;
        	
        	return md5($_SERVER['HTTP_USER_AGENT'] . get_site_secret());
        }
		
	/**
	 * Initialises the system session and potentially logs the user in
	 * 
	 * This function looks for:
	 * 
	 * 1. $_SESSION['id'] - if not present, we're logged out, and this is set to 0
	 * 2. The cookie 'elggperm' - if present, checks it for an authentication token, validates it, and potentially logs the user in 
	 *
	 * @uses $_SESSION
	 * @param unknown_type $event
	 * @param unknown_type $object_type
	 * @param unknown_type $object
	 */
		function session_init($event, $object_type, $object) {
			            
			global $DB_PREFIX, $CONFIG;
			
			if (!is_db_installed()) return false;
                        
            register_shutdown_function('session_write_close');
			
			// Use database for sessions
			if ((!isset($CONFIG->use_file_sessions)))
				session_set_save_handler("__elgg_session_open", "__elgg_session_close", "__elgg_session_read", "__elgg_session_write", "__elgg_session_destroy", "__elgg_session_gc");
				
			session_name('Elgg');
	        session_start();
	        
	        // Do some sanity checking by generating a fingerprint (makes some XSS attacks harder)
	        if (isset($_SESSION['__elgg_fingerprint']))
			{
			    if ($_SESSION['__elgg_fingerprint'] != get_session_fingerprint())
			    {
			    	session_destroy();
			    	return false;
			    }
			}
			else
			{
			    $_SESSION['__elgg_fingerprint'] = get_session_fingerprint();
			}
			
			// Generate a simple token (private from potentially public session id)
			if (!isset($_SESSION['__elgg_session'])) $_SESSION['__elgg_session'] = md5(microtime().rand());
	        
	        if (empty($_SESSION['guid'])) {
	            if (isset($_COOKIE['elggperm'])) {            
	                $code = $_COOKIE['elggperm'];
	                $code = md5($code);
	                unset($_SESSION['guid']);//$_SESSION['guid'] = 0;
	                unset($_SESSION['id']);//$_SESSION['id'] = 0;
	                if ($user = get_user_by_code($code)) {
                    	$_SESSION['user'] = $user;
                        $_SESSION['id'] = $user->getGUID();
                        $_SESSION['guid'] = $_SESSION['id'];
                        $_SESSION['code'] = $_COOKIE['elggperm'];
	                }
	            } else {
	            	unset($_SESSION['id']); //$_SESSION['id'] = 0;
	                unset($_SESSION['guid']);//$_SESSION['guid'] = 0;
	                unset($_SESSION['code']);//$_SESSION['code'] = "";
	            }
	        } else {
	            if (!empty($_SESSION['code'])) {
	                $code = md5($_SESSION['code']);
	                if ($user = get_user_by_code($code)) {
	                	$_SESSION['user'] = $user;
	                	$_SESSION['id'] = $user->getGUID();
                        $_SESSION['guid'] = $_SESSION['id'];
	                } else {
	                	unset($_SESSION['user']);
	                	unset($_SESSION['id']); //$_SESSION['id'] = 0;
		                unset($_SESSION['guid']);//$_SESSION['guid'] = 0;
		                unset($_SESSION['code']);//$_SESSION['code'] = "";
	                }
	            } else {
	            	//$_SESSION['user'] = new ElggDummy();
	            	unset($_SESSION['id']); //$_SESSION['id'] = 0;
	                unset($_SESSION['guid']);//$_SESSION['guid'] = 0;
	                unset($_SESSION['code']);//$_SESSION['code'] = "";
	            }
	        }
	        
	        register_action("login",true);
    		register_action("logout");
    		
    		// Register a default PAM handler
    		register_pam_handler('pam_auth_userpass');
    		
    		// Initialise the magic session
    		global $SESSION;
    		$SESSION = new ElggSession();
    		
    		// Finally we ensure that a user who has been banned with an open session is kicked.
    		if ((isset($_SESSION['user'])) && ($_SESSION['user']->isBanned()))
    		{
    			session_destroy();
			    return false;
    		}
    		
    		// Since we have loaded a new user, this user may have different language preferences
    		register_translations(dirname(dirname(dirname(__FILE__))) . "/languages/");
    		
    		return true;
	        
		}
		
	/**
	 * Used at the top of a page to mark it as logged in users only.
	 *
	 */
		function gatekeeper() {
			if (!isloggedin()) {
				$_SESSION['last_forward_from'] = current_page_url();
				forward();
			}
		}
		
		/**
		 * Used at the top of a page to mark it as logged in admin only.
		 *
		 */
		function admin_gatekeeper()
		{
			gatekeeper();
			if (!isadminloggedin()) {
				$_SESSION['last_forward_from'] = current_page_url();
				forward();
			}
		}
		
		/**
		 * DB Based session handling code.
		 */
		function __elgg_session_open($save_path, $session_name)
		{
			global $sess_save_path;
			$sess_save_path = $save_path;
			
			return true;
		}
		
		/**
		 * DB Based session handling code.
		 */
		function __elgg_session_close()
		{
			return true;
		}
		
		/**
		 * DB Based session handling code.
		 */
		function __elgg_session_read($id)
		{
            $result = get_data_row_2("SELECT * from users_sessions where session=?", array($id));			

            if ($result)
                return (string)$result->data;
				
			return '';
		}
		
		/**
		 * DB Based session handling code.
		 */
		function __elgg_session_write($id, $sess_data)
		{
            if (insert_data_2("REPLACE INTO users_sessions (session, ts, data) VALUES (?,?,?)",
                    array($id, time(), $sess_data))!==false)
            {
                return true;
            }    
			return false;
            
		}
		
		/**
		 * DB Based session handling code.
		 */
		function __elgg_session_destroy($id)
		{
            return (bool)delete_data_2("DELETE from users_sessions where session=?", array($id));
		}
		
		/**
		 * DB Based session handling code.
		 */
		function __elgg_session_gc($maxlifetime)
		{
			$life = time()-$maxlifetime;

            return (bool)delete_data_2("DELETE from users_sessions where ts<?", array($life));
			
			return true;
		}
		
		register_elgg_event_handler("boot","system","session_init",20);


?>