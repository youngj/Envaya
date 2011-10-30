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
                $next = "/org/new?step={$user->setup_state}";
            }
            else
            {
                $next = "{$user->get_url()}/dashboard";
            }
        }

        $next = url_with_param($next, '_lt', timestamp());

        $this->redirect(secure_url($next));    
    }
    
    protected function login_failure()
    {
        throw new ValidationException(view('account/login_error'), true);
    }

    function process_input()
    {
        $username = get_input('username');
        $password = get_input("password");
        $persistent = get_input("persistent", false);
       
        $user = $this->authenticate($username, $password);
        if ($user)
        {
            Session::login($user, array('persistent' => $persistent));
            $this->login_success($user);
        }
        else
        {
            $this->login_failure();
        }
    }

    /**
     * Perform standard authentication with a given username and password.
     * Returns an User object on success, null on failure.
     */
    function authenticate($username, $password)
    {
        if (empty($username) || empty($password))
        {
            return null;
        }        
    
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
            $user->validate_login_rate();
        
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
        return null;            
    }    
    
    function render()
    {
        $username = get_input('username');
        $next = get_input('next');        
        
        $loginTime = (int)get_input('_lt');
        if ($loginTime && timestamp() - $loginTime < 10 && !Session::is_logged_in())
        {
            SessionMessages::add_error_html(view('account/cookie_error'));
        }
        
        $this->page_draw(array(
            'title' => __("login"),            
            'content' => view("account/login", array('username' => $username, 'next' => $next)),
            'org_only' => true,
            'hide_login' => !Session::is_logged_in()
        ));        
    }
}    