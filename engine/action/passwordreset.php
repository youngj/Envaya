<?php

class Action_PasswordReset extends Action
{
    private $user;
    private $conf_code;
    
    function before()
    {
        Permission_Public::require_any();
    
        $user_guid = Input::get_string('u');        
        $user = User::get_by_guid($user_guid);
        if (!$user)
        {
            throw new NotFoundException();
        }    
        
        $conf_code = Input::get_string('c');
        if (!$user->has_password_reset_code($conf_code))
        {
            throw new RedirectException(__('user:password:fail'), "/pg/login");
        }
        $this->conf_code = $conf_code;
        $this->user = $user;
    }

    function process_input()
    {
        $user = $this->user;
        $password = Input::get_string('password');
        $password2 = Input::get_string('password2');

        User::validate_password($password, $password2, 
            $user->get_easy_password_words(),
            $user->get_min_password_strength()
        );

        $user->set_password($password);
        $user->set_password_reset_code(null);
        $user->save();

        LogEntry::create('user:reset_password', $user);
        
        SessionMessages::add(__('user:password:success'));
        Session::login($user);
        $this->redirect("/pg/dashboard");
    }

    function render()
    {    
        $this->prefer_https();
        $this->page_draw(array(
            'title' => __("user:password:choose_new"),
            'content' => view("account/reset_password", array(
                'user' => $this->user,
                'code' => $this->conf_code,
            )),
            'org_only' => true,
        ));                
    }
}    