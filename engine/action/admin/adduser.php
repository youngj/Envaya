<?php

class Action_Admin_AddUser extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $username = get_input('username');
        $password = get_input('password');
        $password2 = get_input('password2');
        $email = get_input('email');
        $name = get_input('name');

        $admin = get_input('admin');
        if (is_array($admin)) $admin = $admin[0];

        if ($password != $password2)
        {
            throw new ValidationException(__('create:passwords_differ'));
        }

        $new_user = register_user($username, $password, $name, $email);
        if ($admin != null)
        {
            $new_user->admin = true;
        }

        $new_user->admin_created = true;
        $new_user->created_by_guid = Session::get_loggedin_userid();
        $new_user->save();

        OutgoingMail::create(
            __('useradd:subject'),
            sprintf(__('useradd:body'), $name, Config::get('sitename'), Config::get('url'), $username, $password)
        )->send_to_user($new_user);                        

        SessionMessages::add(sprintf(__("adduser:ok"), Config::get('sitename')));

        redirect_back();
    }
}    