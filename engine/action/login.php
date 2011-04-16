<?php

class Action_Login extends Action
{
    function process_input()
    {
        $username = get_input('username');
        $password = get_input("password");
        $next = get_input('next');
        $persistent = get_input("persistent", false);

        $result = false;
        if (!empty($username) && !empty($password))
        {
            if ($user = authenticate($username,$password))
            {
                $result = login($user, $persistent);
            }
        }

        if ($result)
        {
            SessionMessages::add(sprintf(__('loginok'), $user->name));

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
        else
        {
            SessionMessages::add_error_html(view('account/login_error'));
            return $this->render();
        }
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
            'hideLogin' => !Session::isloggedin()
        ));        
    }    
}    