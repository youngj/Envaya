<?php

/**
 * Logs in a specified User. 
 *
 * @param User $user A valid user object
 * @param boolean $persistent Should this be a persistent login?
 * @return true|false Whether login was successful
 */
function login($user, $persistent = false)
{
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