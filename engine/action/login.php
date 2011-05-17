<?php

class Action_Login extends Action
{
    protected function login_success($user)
    {
        SessionMessages::add(sprintf(__('login:welcome'), $user->name));

        $next = get_input('next');
        if (!$next)
        {
            if (!$user->is_setup_complete())
            {
                $next = "org/new?step={$user->setup_state}";
            }
            else
            {
                $next = "{$user->get_url()}/dashboard";
            }
        }

        $next = url_with_param($next, '_lt', time());

        forward($next);    
    }
    
    protected function login_failure()
    {
        SessionMessages::add_error_html(view('account/login_error'));
        return $this->render();
    }

    function process_input()
    {
        $username = get_input('username');
        $password = get_input("password");
        $persistent = get_input("persistent", false);

        $result = false;
        if (!empty($username) && !empty($password))
        {
            if ($user = $this->authenticate($username,$password))
            {
                $result = login($user, $persistent);
            }
        }

        if ($result)
        {
            $this->login_success($user);
        }
        else
        {
            $this->login_failure();
        }
    }

    /**
     * Perform standard authentication with a given username and password.
     * Returns an User object for use with login.
     *
     * @see login
     * @param string $username The username
     * @param string $password The password
     * @return User|false The authenticated user object, or false on failure.
     */
    function authenticate($username, $password)
    {
        // if the username has an @, it must be an email address (@ is not allowed in usernames)        
        if (strpos($username,'@') !== false)
        {
            // if there are multiple accounts with the same email address, try all of them, preferring any that is approved
            $users = User::query()->where('email = ?', $username)->order_by('approval desc')->filter();
        }
        else
        {
            $user = User::get_by_username($username);
            $users = $user ? array($user) : array();
        }    
    
        // try all matching users, using the first one with a valid password
        foreach ($users as $user)
        {
            if ($user->has_password($password))
            {
                return $user;
            }            
        }
                
        foreach ($users as $user)
        {
            $user->log_login_failure();
            $user->save();            
        }
        return false;            
    }    
    
    function render()
    {
        $username = get_input('username');
        $next = get_input('next');        
        
        $loginTime = (int)get_input('_lt');
        if ($loginTime && time() - $loginTime < 10 && !Session::isloggedin())
        {
            SessionMessages::add_error_html(view('account/cookie_error'));
        }
        
        $this->page_draw(array(
            'title' => __("login"),            
            'content' => view("account/login", array('username' => $username, 'next' => $next)),
            'org_only' => true,
            'hide_login' => !Session::isloggedin()
        ));        
    }
}    