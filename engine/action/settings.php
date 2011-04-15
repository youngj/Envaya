<?php

class Action_Settings extends Action
{
    function before()
    {
        $this->require_https();
        $this->require_editor();
    }
     
    function process_input()
    {
        $this->validate_security_token();
        
        $user = $this->get_user();

        $name = get_input('name');

        if ($name)
        {
            if (strcmp($name, $user->name)!=0)
            {
                $user->name = $name;
                system_message(__('user:name:success'));
            }
        }
        else
        {
            register_error(__('create:no_name'));
            return $this->render();
        }

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
                system_message(__('user:password:success'));
            }
            else
            {
                register_error(__('user:password:fail:notsame'));
                return $this->render();
            }
        }

        $language = get_input('language');
        if ($language && $language != $user->language)
        {
            $user->language = $language;
            change_viewer_language($user->language);
            system_message(__('user:language:success'));
        }

        $email = trim(get_input('email'));
        if ($email != $user->email)
        {
            try
            {
                validate_email_address($email);
            }
            catch (RegistrationException $ex)
            {
                register_error($ex->getMessage());
                return $this->render();
            }

            $user->email = $email;
            system_message(__('user:email:success'));
        }

        $phone = get_input('phone');
        if ($phone != $user->phone_number)
        {
            $user->set_phone_number($phone);
            system_message(__('user:phone:success'));
        }

        if ($user instanceof Organization)
        {
            $notifications = get_bit_field_from_options(get_input_array('notifications'));
			
            if ($notifications != $user->notifications)
            {
                $user->notifications = $notifications;
                system_message(__('user:notification:success'));
            }
        }

        $user->save();
        forward($user->get_url());
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __("usersettings:user"),
            'content' => view("account/settings", array('entity' => $this->get_user())),
        ));                
    }
    
}    