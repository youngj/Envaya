<?php

class Action_EmailSettings extends Action
{
    private function verify_access($email, $code, $users)
    {
        if (!$email || $code != User::get_email_fingerprint($email) || sizeof($users) == 0)
        {
            throw new RedirectException(__("user:notification:invalid"), "/pg/login");
        }
    }

    function process_input()
    {
        $email = get_input('email');
        $code = get_input('code');
        $notification_type = (int)get_input('notification_type');                
        
        $enabled_notifications = get_input_array('notifications');
        
        $users = User::query()->where('email = ?', $email)->filter();
        
        $this->verify_access($email, $code, $users);

        foreach ($users as $user)
        {
            if ($notification_type)
            {        
                $user->set_notification_enabled($notification_type, in_array($notification_type, $enabled_notifications));
            }
            else
            {
                $user->notifications = get_bit_field_from_options($enabled_notifications);
            }
            $user->save();

            SessionMessages::add(__('user:notification:success'));
        }

        $this->redirect("/pg/dashboard");
    }

    function render()
    {
        $email = get_input('e');
        $code = get_input('c');
        $notification_type = (int)get_input('t');
        
        if (!in_array($notification_type, Notification::all()))
        {
            $notification_type = null;
        }
        
        $users = User::query()->where('email = ?', $email)->filter();

        $this->verify_access($email, $code, $users);
        
        $this->page_draw(array(
            'title' => __("user:notification:label"),
            'content' => view('account/email_settings', array(
                'email' => $email, 
                'users' => $users,
                'notification_type' => $notification_type,
            ))
        ));
    }    
}    