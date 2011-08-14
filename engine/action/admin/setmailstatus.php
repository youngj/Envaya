<?php

class Action_Admin_SetMailStatus extends Action
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
        
        $status = (int)get_input('status');
        
        $mail->status = $status;        
        $mail->save();  
        
        SessionMessages::add(__('email:status_changed'));
        $this->redirect();
    }
}    