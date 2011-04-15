<?php

class Action_PasswordReset extends Action
{
    function process_input()
    {
        $user_guid = get_input('u');
        $conf_code = get_input('c');
        $user = get_user($user_guid);

        if ($user && $user->passwd_conf_code && $user->passwd_conf_code == $conf_code)
        {
            $password = get_input('password');
            $password2 = get_input('password2');
            if ($password!="")
            {
                try
                {
                    validate_password($password);
                }
                catch (RegistrationException $ex)
                {
                    register_error($ex->getMessage());
                    return $this->render();
                }

                if ($password == $password2)
                {
                    $user->set_password($password);
                    $user->passwd_conf_code = null;
                    $user->save();
                    system_message(__('user:password:success'));
                    login($user);
                    forward("pg/dashboard");
                }
                else
                {
                    register_error(__('user:password:fail:notsame'));
                    return $this->render();
                }
            }
        }
        else
        {
            register_error(__('user:password:fail'));
            forward("pg/login");
        }
    }

    function render()
    {    
        $this->require_https();

        $user_guid = get_input('u');
        $conf_code = get_input('c');

        $user = get_user($user_guid);

        if ($user && $user->passwd_conf_code && $user->passwd_conf_code == $conf_code)
        {
            $this->page_draw(array(
                'title' => __("user:password:choose_new"),
                'content' => view("account/reset_password", array('entity' => $user)),
                'org_only' => true,
            ));                
        }
        else
        {
            register_error(__('user:password:fail'));
            forward("pg/login");
        }
    }
}    