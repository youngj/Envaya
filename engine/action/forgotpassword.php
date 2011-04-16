<?php

class Action_ForgotPassword extends Action
{
    function process_input()
    {
        $username = get_input('username');

        $user = User::get_by_username($username);

        if ($user)
        {
            if (!$user->email)
            {
                SessionMessages::add_error(__('user:password:resetreq:no_email'));
                forward("page/contact");
            }

            $user->passwd_conf_code = substr(generate_random_cleartext_password(), 0, 24); // avoid making url too long for 1 line in email
            $user->save();

            $mail = OutgoingMail::create(
                __('email:resetreq:subject',$user->language),
                view('emails/password_reset_request', array('user' => $user))
            );
            
            if ($mail->send_to_user($user))
            {
                SessionMessages::add(__('user:password:resetreq:success'));
            }
            else
            {
                SessionMessages::add_error(__('user:password:resetreq:fail'));
                return $this->render();
            }
        }
        else
        {
            SessionMessages::add_error(sprintf(__('user:username:notfound'), $username));
            return $this->render();
        }

        forward();
    }

    function render()
    {    
        $this->page_draw(array(
            'title' => __('user:password:reset'),
            'content' => view("account/forgotten_password",
                array('username' => get_input('username'))
            ),
            'org_only' => true
        ));
    }    
}    