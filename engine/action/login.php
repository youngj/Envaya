<?php

class Action_Login extends Action
{
    protected function login_success($user, $password)
    {
        SessionMessages::add(sprintf(__('login:welcome'), $user->name));

        $next = get_input('next');
        if (!$next && !$user->is_setup_complete())
        {
            $next = $user->get_continue_setup_url();
        }
        
        if (!$next)
        {
            $next = "{$user->get_url()}/dashboard";
        }

        $min_password_strength = $user->get_min_password_strength();
        
        $password_age = $user->get_password_age();
        $max_password_age = $user->get_max_password_age();
        
        $new_password_url = "{$user->get_url()}/password?next=".urlencode($next);
            
        if ($max_password_age && $password_age > $max_password_age)
        {
            SessionMessages::add_error(__('user:password:too_old'));
            $next = $new_password_url;
        }                        
        else if ($min_password_strength && PasswordStrength::calculate($password) < $min_password_strength)
        {
            SessionMessages::add_error(__('register:password_too_easy'));
            $next = $new_password_url;
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
            $this->login_success($user, $password);
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
        $this->prefer_https();

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
