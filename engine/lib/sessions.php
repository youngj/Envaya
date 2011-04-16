<?php

/**
 * Perform standard authentication with a given username and password.
 * Returns an User object for use with login.
 *
 * @see login
 * @param string $username The username, optionally (for standard logins)
 * @param string $password The password, optionally (for standard logins)
 * @return User|false The authenticated user object, or false on failure.
 */
function authenticate($username, $password)
{
    if ($username && $password)
    {
        if ($user = User::get_by_username($username))
        {
            if ($user->is_banned())
            {
                return false;
            }

            if ($user->password == $user->generate_password($password))
            {
                return $user;
            }

            $user->log_login_failure();
            $user->save();
        }
    }

    return false;

}

/**
 * Logs in a specified User. For standard registration, use in conjunction
 * with authenticate.
 *
 * @see authenticate
 * @param User $user A valid user object
 * @param boolean $persistent Should this be a persistent login?
 * @return true|false Whether login was successful
 */
function login($user, $persistent = false)
{
    if ($user->is_banned())
        return false;

    if ($user->check_rate_limit_exceeded())
        return false;

    Session::set('guid', $user->guid);

    if ($persistent)
    {
        session_set_cookie_params(60 * 60 * 24 * 60);
    }

    // Users privilege has been elevated, so change the session id (help prevent session hijacking)
    session_regenerate_id(true);

    EventRegister::trigger_event('login','user',$user);
    
    $user->reset_login_failure_count();
    $user->last_login = time();
    $user->last_action = time();
    $user->save();    

    return true;
}


function logout()
{
    $curUser = Session::get_loggedin_user();
    if ($curUser)
    {
        trigger_event('logout','user',$curUser);
    }

    Session::destroy();
    return true;
}

function force_login()
{
    $next = Request::instance()->full_rewritten_url();
    $username = get_input('username');
    $loginTime = get_input('_lt');
    
    $args = array();
    if ($username)
    {
        $args[] = "username=".urlencode($username);
    }
    if ($next)
    {
        $args[] = "next=".urlencode($next);
    }
    if ($loginTime)
    {
        $args[] = '_lt='.urlencode($loginTime);
    }
    
    if ($args)
    {
        forward("pg/login?".implode("&", $args));
    }
    else
    {
        forward("pg/login");
    }
}

function restore_input($name, $value, $trackDirty = false)
{
    if (isset($_POST[$name]))
    {
        return $_POST[$name];
    }

    $prevInput =  Session::get('input');
    if ($prevInput)
    {
        if (isset($prevInput[$name]))
        {
            $val = $prevInput[$name];
            unset($prevInput[$name]);
            Session::set('input', $prevInput);
            
            if ($trackDirty && $val != $value)
            {
                PageContext::set_dirty(true);
            }
            
            return $val;
        }
    }
    return $value;
}