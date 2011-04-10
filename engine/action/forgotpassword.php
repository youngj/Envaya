<?php

class Action_ForgotPassword extends Action
{
    function process_input()
    {
        $username = get_input('username');

        $user = get_user_by_username($username);
        if (!$user)
        {
            $user = User::query()->where('email = ?', $username)->get();
        }

        if ($user)
        {
            if (!$user->email)
            {
                register_error(__('user:password:resetreq:no_email'));
                forward("page/contact");
            }

            $user->passwd_conf_code = substr(generate_random_cleartext_password(), 0, 24); // avoid making url too long for 1 line in email
            $user->save();

            $mail = Zend::mail(
                __('email:resetreq:subject',$user->language),
                view('emails/password_reset_request', array('user' => $user))
            );
            
            if ($user->send_mail($mail))
            {
                system_message(__('user:password:resetreq:success'));
            }
            else
            {
                register_error(__('user:password:resetreq:fail'));
                return $this->render();
            }
        }
        else
        {
            register_error(sprintf(__('user:username:notfound'), $username));
            return $this->render();
        }

        forward();
    }

    function render()
    {    
        $body = view("account/forms/forgotten_password",
            array('username' => get_input('username'))
        );

        $title = __('user:password:reset');
        $this->page_draw($title, view_layout("one_column",
            view_title($title, array('org_only' => true)), $body));
    }    
}    