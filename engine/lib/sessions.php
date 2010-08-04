<?php

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
    $user = get_loggedin_user();

    return ($user && ($user instanceof ElggUser) && ($user->guid > 0));
}

function isadminloggedin()
{    
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
    if ($username && $password)
    {
        if ($user = get_user_by_username($username))
        {
            if ($user->isBanned())
            {
                return false;
            }

            if ($user->password == generate_user_password($user, $password))
            {
                return $user;
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
        $fails = (int)$user->login_failures;
        $fails++;

        $user->login_failures = $fails;
        $user->set("login_failure_$fails", time());
    }
}

function reset_login_failure_count($user_guid)
{
    $user_guid = (int)$user_guid;
    $user = get_entity($user_guid);

    if (($user_guid) && ($user) && ($user instanceof ElggUser))
    {
        $fails = (int)$user->login_failures;

        if ($fails) {
            for ($n=1; $n <= $fails; $n++)
                $user->set("login_failure_$n", null);

            $user->login_failures = null;
        }
        $user->save();
    }
}

function check_rate_limit_exceeded($user_guid)
{
    $limit = 5;
    $user_guid = (int)$user_guid;
    $user = get_entity($user_guid);

    if (($user_guid) && ($user) && ($user instanceof ElggUser))
    {
        $fails = (int)$user->login_failures;
        if ($fails >= $limit)
        {
            $cnt = 0;
            $time = time();
            for ($n=$fails; $n>0; $n--)
            {
                $f = $user->get("login_failure_$n");
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

    if ($user)
    {
        EventRegister::trigger_event('login','user',$user);
    }    
    
    set_last_login($user->guid);
    reset_login_failure_count($user->guid);

    return true;
}


function logout()
{
    $curUser = get_loggedin_user();
    if ($curUser)
    {
        trigger_event('logout','user',$curUser);
    }

    Session::destroy();

    setcookie("elggperm", "", (time()-(86400 * 30)),"/");

    return true;
}

function force_login()
{
    Session::set('last_forward_from', Request::instance()->full_rewritten_url());
    $username = get_input('username');
    if ($username)
    {
        forward("pg/login?username=".urlencode($username));
    }
    else
    {
        forward("pg/login");
    }
}

