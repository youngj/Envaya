<?php

class Action_EmailSettings extends Action
{
    function process_input()
    {
        $email = get_input('email');
        $code = get_input('code');
        $notifications = get_bit_field_from_options(get_input_array('notifications'));
        $users = User::query()->where('email = ?', $email)->filter();

        foreach ($users as $user)
        {
            $user->notifications = $notifications;
            $user->save();

            system_message(__('user:notification:success'));
        }

        forward("/");
    }

    function render()
    {
        $email = get_input('e');
        $code = get_input('c');
        $users = User::query()->where('email = ?', $email)->filter();

        $title = __("user:notification:label");

        if ($email && $code == get_email_fingerprint($email) && sizeof($users) > 0)
        {
            $area1 = view('org/emailSettings', array('email' => $email, 'users' => $users));
        }
        else
        {
            $area1 = __("user:notification:invalid");
        }
        $body = view_layout("one_column_padded", view_title($title), $area1);

        $this->page_draw($title, $body); 
    }    
}    