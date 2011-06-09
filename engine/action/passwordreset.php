<?php

class Action_PasswordReset extends Action
{
    private $user;
    
    function before()
    {
        $user_guid = get_input('u');        
        $user = User::get_by_guid($user_guid);
        if (!$user)
        {
            throw new NotFoundException();
        }    
        
        $conf_code = get_input('c');
        $correct_code = $user->get_metadata('passwd_conf_code');
        if (!$correct_code || $correct_code != $conf_code)
        {
            throw new RedirectException(__('user:password:fail'), "/pg/login");
        }
        $this->user = $user;
    }

    function process_input()
    {
        $user = $this->user;
        $password = get_input('password');
        $password2 = get_input('password2');

        User::validate_password($password, $password2, $user->name, $user->username);

        $user->set_password($password);
        $user->set_metadata('passwd_conf_code', null);
        $user->save();

        SessionMessages::add(__('user:password:success'));
        login($user);
        $this->redirect("/pg/dashboard");
    }

    function render()
    {    
        $this->prefer_https();
        $this->page_draw(array(
            'title' => __("user:password:choose_new"),
            'content' => view("account/reset_password", array('entity' => $this->user)),
            'org_only' => true,
        ));                
    }
}    