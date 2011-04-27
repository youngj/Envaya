<?php

class Action_ForgotPassword extends Action
{
    function process_input()
    {
        $username = get_input('username');

        // if the username has an @, it must be an email address (@ is not allowed in usernames)        
        if (strpos($username,'@') !== false)
        {
            // if there are multiple accounts with the same email address, we just return one of them, preferring any that is approved
            $user = User::query()->where('email = ?', $username)->order_by('approval desc')->get();
        }
        else
        {
            $user = User::get_by_username($username);
        }
        
        
        if ($user)
        {
            if (!$user->email)
            {
                SessionMessages::add_error(__('login:resetreq:no_email'));
                forward("page/contact");
            }

            $user->set_metadata('passwd_conf_code', generate_random_code(24)); // avoid making url too long for 1 line in email
            $user->save();

            $mail = OutgoingMail::create(
                __('login:resetreq:subject',$user->language),
                view('emails/password_reset_request', array('user' => $user))
            );
            
            $mail->send_to_user($user);
            SessionMessages::add(__('login:resetreq:success'));
        }
        else
        {
            SessionMessages::add_error(sprintf(__('login:resetreq:notfound'), $username));
            return $this->render();
        }

        forward();
    }

    function render()
    {    
        $this->page_draw(array(
            'title' => __('login:resetreq:title'),
            'content' => view("account/forgotten_password",
                array('username' => get_input('username'))
            ),
            'org_only' => true
        ));
    }    
}    