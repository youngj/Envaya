<?php

class Action_EmailTemplate_ResetOutgoingMail extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $email = $this->get_email();
        
        $id = (int)get_input('id');
        $outgoing_mail = OutgoingMail::query()->where('id = ?', $id)->get();
        if (!$outgoing_mail)
        {
            throw new NotFoundException();
        }
        
        $outgoing_mail->email_guid = 0;
        $outgoing_mail->save();
        
        $email->update();
        
        $this->redirect();
    }
}    