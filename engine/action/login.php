<?php

class Action_Login extends Action
{
    function before()
    {
        Permission_Public::require_any();
    }

    protected function login_success($user, $password)
    {
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
        $password_strength = PasswordStrength::calculate($password, $user->get_easy_password_words());
        
        $password_age = $user->get_password_age();
        $max_password_age = $user->get_max_password_age();        
        
        $new_password_url = "{$user->get_url()}/password?next=".urlencode($next);
            
        if ($max_password_age && $password_age > $max_password_age)
        {
            SessionMessages::add_error(__('user:password:too_old'));
            $next = $new_password_url;
        }                        
        else if ($min_password_strength && $password_strength < $min_password_strength)
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
    
        $attempts_remaining = min(
            User::get_login_attempts_remaining_for_ip(),
            User::get_login_attempts_remaining($username)
        );
        if ($attempts_remaining <= 0)
        {
            throw new ValidationException(view('account/login_rate_exceeded'), true);
        }        
    
        // try all matching users, using the first one with a valid password
        foreach ($users as $user)
        {
            $attempts_remaining = min(User::get_login_attempts_remaining($user->username), $attempts_remaining);
            if ($attempts_remaining <= 0)
            {
                throw new ValidationException(view('account/login_rate_exceeded'), true);
            }
            
            if ($user->has_password($password))
            {
                return $user;
            }
        }
        User::log_login_failure($username, @$users[0]);       
        if ($attempts_remaining <= 1)
        {
            throw new ValidationException(view('account/login_rate_exceeded'), true);
        }
        
        return null;            
    }    
    
    function render_content()
    {
        $username = get_input('username');
        $next = get_input('next');            
        
        return view("account/login", array('username' => $username, 'next' => $next));
    }    
    
    function render()
    {
        $this->prefer_https();
        
        $loginTime = (int)get_input('_lt');
        if ($loginTime && timestamp() - $loginTime < 10 && !Session::is_logged_in())
        {
            SessionMessages::add_error_html(view('account/cookie_error'));
        }
        
        $this->page_draw(array(
            'title' => __("login"),            
            'content' => $this->render_content(),
            'org_only' => true,
            'hide_login' => !Session::is_logged_in()
        ));        
    }
}    
