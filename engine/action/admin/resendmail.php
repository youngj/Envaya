<?php

class Action_Admin_ResendMail extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {        
        $id = get_input('id');
        
        $mail = OutgoingMail::query()->where('id = ?', $id)->get();
        if (!$mail)
        {
            throw new NotFoundException();
        }        
        $mail->send(true);        
        SessionMessages::add(__('email:sent_ok'));
        $this->redirect('/admin/outgoing_mail');
    }
}    