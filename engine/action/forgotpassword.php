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
        
        if (!$user)
        {
            $phone_number = PhoneNumber::canonicalize($username, GeoIP::get_country_code());        
            //var_dump($phone_number);
            $user = $this->get_user_by_phone_number($phone_number);
            if ($user)
            {
                $this->send_code_to_phone_number($phone_number, $user);
                return;
            }
        }
                        
        if ($user)
        {
            if ($user->email)
            {
                $code = generate_random_code(10);            
                
                $user->set_password_reset_code($code); 
                $user->save();

                $mail = OutgoingMail::create(
                    __('login:resetreq:subject',$user->language),
                    view('emails/password_reset_request', array('user' => $user, 'code' => $code))
                );
                
                $mail->send_to_user($user);
                SessionMessages::add(__('login:resetreq:success'));
                $this->redirect('/');                            
            }            
            else
            {
                $user_phone_number = $user->query_phone_numbers()->get();
                if ($user_phone_number)
                {
                    $this->send_code_to_phone_number($user_phone_number->phone_number, $user);
                }
                else
                {            
                    throw new RedirectException(__('login:resetreq:no_email'), "/page/contact");
                }
            }
        }
        else
        {
            throw new ValidationException(sprintf(__('login:resetreq:notfound'), $username));
        }
    }

    function send_code_to_phone_number($phone_number, $user)
    {    
        $code = generate_random_code(10);
        $user->set_password_reset_code($code);
        $user->save();
    
        $sms = SMS_Service_Contact::create_outgoing_sms(
            $phone_number, 
            "$code\n\n".__('user:password:reset_sms'));
        $sms->message_type = OutgoingSMS::Transactional;
        $sms->send();
    
        SessionMessages::add(__('login:resetreq:sms_sent'));
        $this->redirect("/pg/password_reset_code?u={$user->guid}");
    }
    
    function get_user_by_phone_number($phone_number)
    {        
        if (!$phone_number)
        {
            return null;
        }
        $last_digits = UserPhoneNumber::get_last_digits($phone_number);
            
        $user_phone_number = UserPhoneNumber::query()
            ->where('last_digits = ?', $last_digits)
            ->where('phone_number = ?', $phone_number)
            ->get();
                
        if (!$user_phone_number)
        {
            return null;
        }
        
        return $user_phone_number->get_user();
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