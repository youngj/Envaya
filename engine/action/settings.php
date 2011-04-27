<?php

class Action_Settings extends Action
{
    function before()
    {
        $this->prefer_https();
        $this->require_editor();
    }
     
    function process_input()
    {
        $user = $this->get_user();

        $name = get_input('name');

        if ($name)
        {
            if (strcmp($name, $user->name)!=0)
            {
                $user->name = $name;
                SessionMessages::add(__('user:name:success'));
            }
        }
        else
        {
            SessionMessages::add_error(__('register:no_name'));
            return $this->render();
        }

        $password = get_input('password');
        $password2 = get_input('password2');
        if ($password!="")
        {
            validate_password($password);

            if ($password == $password2)
            {
                $user->set_password($password);
                SessionMessages::add(__('user:password:success'));
            }
            else
            {
                SessionMessages::add_error(__('user:password:fail:notsame'));
                return $this->render();
            }
        }

        $language = get_input('language');
        if ($language && $language != $user->language)
        {
            $user->language = $language;
            change_viewer_language($user->language);
            SessionMessages::add(__('user:language:success'));
        }

        $email = trim(get_input('email'));
        if ($email != $user->email)
        {
            $user->email = validate_email_address($email);
            SessionMessages::add(__('user:email:success'));
        }

        $phone = get_input('phone');
        if ($phone != $user->phone_number)
        {
            $user->set_phone_number($phone);
            SessionMessages::add(__('user:phone:success'));
        }

        if ($user instanceof Organization)
        {
            $notifications = get_bit_field_from_options(get_input_array('notifications'));
			
            if ($notifications != $user->notifications)
            {
                $user->notifications = $notifications;
                SessionMessages::add(__('user:notification:success'));
            }
        }

        $user->save();
        forward($user->get_url());
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __('user:settings'),
            'content' => view("account/settings", array('entity' => $this->get_user())),
        ));                
    }
    
}    