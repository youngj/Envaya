<?php

class Action_User_Settings extends Action
{
    function before()
    {
        $this->require_site_editor();
    }
     
    function process_input()
    {
        $user = $this->get_user();
        
        if (Session::isadminloggedin() && get_input('delete'))
        {
            $user->disable();
            $user->save();
            SessionMessages::add(__('user:deleted'));
            return $this->redirect('/admin/user');
        }

        $name = get_input('name');

        if ($name)
        {
            if ($name != $user->name)
            {
                $user->name = $name;
                SessionMessages::add(__('user:name:success'));
            }
        }
        else
        {
            throw new ValidationException(__('register:no_name'));
        }

        $language = get_input('language');
        if ($language && $language != $user->language)
        {
            $user->language = $language;
            $this->change_viewer_language($user->language);
            SessionMessages::add(__('user:language:success'));
        }

        $email = trim(get_input('email'));
        if ($email != $user->email)
        {
            $user->set_email(EmailAddress::validate($email));
            SessionMessages::add(__('user:email:success'));
        }

        $phone = get_input('phone');
        if ($phone != $user->phone_number)
        {
            $user->set_phone_number($phone);
            SessionMessages::add(__('user:phone:success'));
        }

        $user->save();
        $this->redirect(get_input('from') ?: $user->get_url());
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __('user:settings'),
            'content' => view("account/settings", array('user' => $this->get_user())),
        ));                
    }    
}    