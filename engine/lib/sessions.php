<?php

class Session
{
    private static $started = false;  
    private static $dirty = false;
    private static $cookieName = 'envaya';

    static function get($key)
    {
        if (static::$started)
        {
            return @$_SESSION[$key];
        }    
        else if (@$_COOKIE[static::$cookieName])
        {
            static::start();
            return @$_SESSION[$key];
        }
        else
        {
            return null;
        }
    }
    
    static function destroy()
    {
        session_destroy();
        setcookie(static::$cookieName, "", 0,"/");
    }
    
    static function id()
    {
        if (static::$started)
        {
            return session_id();
        }
        if (!static::$started && @$_COOKIE[static::$cookieName])
        {
            static::start();
            return session_id();
        }
        return null;
        
    }
    
    static function saveInput()
    {
        static::set('input', $_POST);
    }
    
    static function start()
    {
        session_set_save_handler("__elgg_session_open", "__elgg_session_close", "__elgg_session_read", "__elgg_session_write", "__elgg_session_destroy", "__elgg_session_gc");
    
        session_name(static::$cookieName);
        session_start();
        static::$started = true;    
        
        register_elgg_event_handler('shutdown', 'system', 'session_write_close', 10);

        $fingerprint = @$_SESSION['__elgg_fingerprint'];
        if ($fingerprint)
        {
            if ($fingerprint != get_session_fingerprint())
            {
                session_destroy();
                session_start();
            }
        }
        else
        {
            static::set('__elgg_fingerprint', get_session_fingerprint());            
        }

        if (!isset($_SESSION['__elgg_session'])) 
        {
            static::set('__elgg_session', md5(microtime().rand()));
        }    
        
        $guid = @$_SESSION['guid'];
        if ($guid) 
        {        
            $user = get_user($guid);
            if (!$user || $user->isBanned())
            {
                static::set($_SESSION['guid'], null);
            }
        }
    }
    
    static function isDirty()
    {
        return static::$dirty;
    }
    
    static function set($key, $value)
    {
        if (!static::$started)
        {
            static::start();
        }
        if (is_null($value))
        {
            unset($_SESSION[$key]);
        }
        else
        {
            $_SESSION[$key] = $value;
        }
        static::$dirty = true;
    }    
}

function get_loggedin_user()
{			
    $guid = Session::get('guid');
    if ($guid)
    {
        return get_entity($guid);
    }

    return null;
}

function get_loggedin_userid()
{
    $user = get_loggedin_user();
    if ($user)
        return $user->guid;

    return 0;
}

function isloggedin() 
{ 						
    if (!is_installed()) 
        return false; 

    $user = get_loggedin_user();

    return ($user && ($user instanceof ElggUser) && ($user->guid > 0));
}

function isadminloggedin()
{
    if (!is_installed()) 
        return false; 

    $user = get_loggedin_user();
    return ($user && $user->admin);
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
function authenticate($username, $password) 
{            
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
        if ($user = get_user_by_username($credentials['username'])) 
        {                 
            if ($user->isBanned()) 
            {
                return false;
            }

            if ($user->password == generate_user_password($user, $credentials['password'])) 
            {
                return true;
            }    

            log_login_failure($user->guid);
        }
    }

    return false;
}

function log_login_failure($user_guid)
{
    $user_guid = (int)$user_guid;
    $user = get_entity($user_guid);

    if ($user_guid && $user && ($user instanceof ElggUser))
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
function login(ElggUser $user, $persistent = false) 
{
    if ($user->isBanned()) 
        return false; 

    /*
    if (check_rate_limit_exceeded($user->guid))
        return false; 
    */       

    Session::set('guid', $user->getGUID());
    
    if ($persistent)
    {
        session_set_cookie_params(60 * 60 * 24 * 60);
    }
    
    // Users privilege has been elevated, so change the session id (help prevent session hijacking)
    session_regenerate_id(true); 

    set_last_login($user->guid);
    reset_login_failure_count($user->guid); 

    return true;				
}


function logout() 
{
    $curUser = get_loggedin_user();
    if ($curUser) 
    {
        if (!trigger_elgg_event('logout','user',$curUser)) 
            return false;
    }

    Session::destroy();

    setcookie("elggperm", "", (time()-(86400 * 30)),"/");

    return true;
}

function get_session_fingerprint()
{
    $userAgent = @$_SERVER['HTTP_USER_AGENT'];

    return md5($userAgent . get_site_secret());
}

/**
 * Initialises the system session and potentially logs the user in
 * 
 * This function looks for:
 * 
 * 1. $_SESSION['guid'] - if not present, we're logged out, and this is set to 0
 * 2. The cookie 'elggperm' - if present, checks it for an authentication token, validates it, and potentially logs the user in 
 *
 * @uses $_SESSION
 * @param unknown_type $event
 * @param unknown_type $object_type
 * @param unknown_type $object
 */
function session_init($event, $object_type, $object) 
{			            
    register_pam_handler('pam_auth_userpass');

    return true;	        
}

function gatekeeper() 
{
    if (!isloggedin()) 
    {
        Session::set('last_forward_from', current_page_url());
        forward("pg/login");
    }
}

function admin_gatekeeper()
{
    gatekeeper();
    if (!isadminloggedin()) 
    {
        Session::set('last_forward_from', current_page_url());
        forward("pg/login");
    }
}

function __elgg_session_open($save_path, $session_name)
{
    global $sess_save_path;
    $sess_save_path = $save_path;

    return true;
}

function __elgg_session_close()
{
    return true;
}

function session_cache_key($sessionId)
{
    return make_cache_key("session", $sessionId);
}

function __elgg_session_read($id)
{
    $cacheKey = session_cache_key($id);
    $sessionData = get_cache()->get($cacheKey);
    
    if ($sessionData == null)
    {
        $result = get_data_row("SELECT * from users_sessions where session=?", array($id));			
        $sessionData = ($result) ? $result->data : '';
        get_cache()->set($cacheKey, $sessionData);
    }    
    
    return $sessionData;
}

function __elgg_session_write($id, $sess_data)
{
    if (Session::isDirty())
    {
        get_cache()->set(session_cache_key($id), $sess_data);

        return (insert_data("REPLACE INTO users_sessions (session, ts, data) VALUES (?,?,?)",
                array($id, time(), $sess_data))!==false);
    }    
}

function __elgg_session_destroy($id)
{
    get_cache()->delete(session_cache_key($id));

    return (bool)delete_data("DELETE from users_sessions where session=?", array($id));
}

function __elgg_session_gc($maxlifetime)
{
    $life = time()-$maxlifetime;

    return (bool)delete_data("DELETE from users_sessions where ts<?", array($life));

    return true;
}

register_elgg_event_handler("boot","system","session_init",20);
