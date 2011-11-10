<?php

class Action_User_ChangePassword extends Action
{
    function before()
    {
        Permission_EditUserSettings::require_for_entity($this->get_user());
    }
    
    function require_old_password()
    {
        $user = $this->get_user();
                
        if ($user->equals(Session::get_logged_in_user()))
        {
            // don't require old password if user just logged in 
            $max_login_age = Request::is_post() ? 360 : 180;
            $login_age = Session::get_login_age();
            
            if (Session::is_consistent_client() && $login_age !== null && $login_age < $max_login_age)
            {
                return false;
            }            
            else
            {        
                return true;
            }
        }        
        
        // old password not necessary for someone with EditUserSettings permission to 
        // change password of account that does not have permissions        
        return false;
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
        
        User::validate_password(
            $password, 
            $password2, 
            $user->get_easy_password_words(),
            $user->get_min_password_strength()
        );

        if ($user->has_password($password))
        {
            throw new ValidationException(__('user:password:not_changed'));
        }
        
        $user->set_password($password);
        $user->set_password_reset_code(null);
        $user->save();

        SessionMessages::add(__('user:password:success'));
        $this->redirect(get_input('next') ?: $user->get_url()."/settings");
    }

    function render()
    {    
        $this->prefer_https();
        
        $this->use_editor_layout();

        $user = $this->get_user();

        $cancelUrl = $user->get_url()."/settings";
        PageContext::get_submenu('edit')->add_link(__("cancel"), $cancelUrl);
        
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