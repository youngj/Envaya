<?php

class Action_User_SendMessage extends Action
{
    function before()
    {
        Permission_RegisteredUser::require_any();
        $this->use_editor_layout();
    }
     
    function process_input()
    {
        $from_user = Session::get_logged_in_user();        
        $to_user = $this->get_user();

        $subject = get_input('subject');
        if (!$subject)
        {
            throw new ValidationException(__("message:subject_missing"));
        }

        $message = get_input('message');
        if (!$message)
        {
            throw new ValidationException(__("message:empty"));
        }

        $mail = OutgoingMail::create($subject, $message);
        $mail->set_from_user($from_user);
        $mail->add_bcc($to_user->email);
        
        if ($mail->send_to_user($to_user))
        {
            SessionMessages::add(
                ($mail->status == OutgoingMail::Held) ? __('message:held') : __('message:sent')
            );
        }
        else
        {
            throw new ValidationException(__("message:not_sent"));
        }

        $this->redirect($to_user->get_url());
    }

    function render()
    {
        $user = $this->get_user();
        
        PageContext::get_submenu('edit')->add_link(
            __("message:cancel"), 
            $user->get_url()
        );

        $this->page_draw(array(
            'title' => __("message:title"),
            'content' => view("account/compose_message", array('user' => $user)),
        ));        
    }
    
}    