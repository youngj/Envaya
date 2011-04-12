<?php

class Action_SendMessage extends Action
{
    function before()
    {
        $this->require_login();
        $this->use_editor_layout();
        $this->require_org();
        
        $user = Session::get_loggedin_user();

        if (!$user->is_approved())
        {
            register_error(__('message:needapproval'));
            forward_to_referrer();
        }
    }
     
    function process_input()
    {
        $this->validate_security_token();

        $user = Session::get_loggedin_user();

        $recipient = $this->get_org();

        if (!$recipient)
        {
            register_error(__("message:invalid_recipient"));
            return $this->render();
        }
        else
        {
            $subject = get_input('subject');
            if (!$subject)
            {
                register_error(__("message:subject_missing"));
                return $this->render();
            }

            $message = get_input('message');
            if (!$message)
            {
                register_error(__("message:message_missing"));
                return $this->render();
            }

            $mail = Zend::mail($subject, $message);
            $mail->setFrom(Config::get('email_from'), $user->name);
            $mail->setReplyTo($user->email, $user->name);
            $mail->addBcc($user->email);
            
            if ($recipient->send_mail($mail))
            {
                system_message(__("message:sent"));
            }
            else
            {
                register_error(__("message:not_sent"));
                return $this->render();
            }

            forward($recipient->get_url());
        }
    }

    function render()
    {
        $org = $this->get_org();
        
        $user = Session::get_loggedin_user();

        PageContext::get_submenu('edit')->add_item(__("message:cancel"), $org->get_url());

        $title = __("message:title");
        $area1 = view("org/composeMessage", array('entity' => $org, 'user' => $user));
        $body = view_layout("one_column", view_title($title), $area1);
        $this->page_draw($title,$body);  
    }
    
}    