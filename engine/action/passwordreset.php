<?php

class Action_PasswordReset extends Action
{
    function process_input()
    {
        $user_guid = get_input('u');
        $conf_code = get_input('c');
        $user = User::get_by_guid($user_guid);

        $correct_code = $user->get_metadata('passwd_conf_code');
        
        if ($user && $correct_code && $correct_code == $conf_code)
        {
            $password = get_input('password');
            $password2 = get_input('password2');

            User::validate_password($password, $password2, $user->name, $user->username);

            $user->set_password($password);
            $user->set_metadata('passwd_conf_code', null);
            $user->save();
            SessionMessages::add(__('user:password:success'));
            login($user);
            forward("pg/dashboard");
        }
        else
        {
            SessionMessages::add_error(__('user:password:fail'));
            forward("pg/login");
        }
    }

    function render()
    {    
        $this->prefer_https();

        $user_guid = get_input('u');
        $conf_code = get_input('c');

        $user = User::get_by_guid($user_guid);

        $correct_code = $user->get_metadata('passwd_conf_code');
        if ($user && $correct_code && $correct_code == $conf_code)
        {
            $this->page_draw(array(
                'title' => __("user:password:choose_new"),
                'content' => view("account/reset_password", array('entity' => $user)),
                'org_only' => true,
            ));                
        }
        else
        {
            SessionMessages::add_error(__('user:password:fail'));
            forward("pg/login");
        }
    }
}    