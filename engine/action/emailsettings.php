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
        $notifications = get_bit_field_from_options(get_input_array('notifications'));
        $users = User::query()->where('email = ?', $email)->filter();
        
        $this->verify_access($email, $code, $users);

        foreach ($users as $user)
        {
            $user->notifications = $notifications;
            $user->save();

            SessionMessages::add(__('user:notification:success'));
        }

        $this->redirect("/pg/login");
    }

    function render()
    {
        $email = get_input('e');
        $code = get_input('c');
        $users = User::query()->where('email = ?', $email)->filter();

        $this->verify_access($email, $code, $users);
        
        $this->page_draw(array(
            'title' => __("user:notification:label"),
            'content' => view('account/email_settings', array('email' => $email, 'users' => $users))
        ));
    }    
}    