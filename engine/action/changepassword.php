<?php

class Action_ChangePassword extends Action
{
    function before()
    {
        $this->require_editor();
    }
    
    function require_old_password()
    {
        return !Session::isadminloggedin() || $this->get_user()->admin;
    }

    function process_input()
    {
        $old_password = get_input('old_password');
        
        $user = $this->get_user();
                
        if ($this->require_old_password() && !$user->has_password($old_password))
        {
            throw new ValidationException(__('user:password:current:incorrect'));
        }
        
        $password = get_input('password');
        $password2 = get_input('password2');

        User::validate_password($password, $password2, $user->name, $user->username);

        $user->set_password($password);
        $user->set_metadata('passwd_conf_code', null);
        $user->save();

        SessionMessages::add(__('user:password:success'));
        $this->redirect($user->get_url()."/settings");
    }

    function render()
    {    
        $this->prefer_https();

        $user = $this->get_user();

        $cancelUrl = $user->get_url()."/settings";
        PageContext::get_submenu('edit')->add_item(__("cancel"), $cancelUrl);
        
        $this->page_draw(array(
            'title' => __("user:password:change"),
            'content' => view("account/change_password", array(
                'user' => $user,
                'require_old_password' => $this->require_old_password()
            )),
            'org_only' => true,
        ));                
    }
}    