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
