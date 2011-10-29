<?php

class Action_Admin_ResendSMS extends Action
{
    function before()
    {
        Permission_SendMessage::require_for_root();
    }
     
    function process_input()
    {        
        $id = get_input('id');
        
        $sms = OutgoingSMS::query()->where('id = ?', $id)->get();
        if (!$sms)
        {
            throw new NotFoundException();
        }        
        $sms->send(true);        
        
        if ($sms->status == OutgoingSMS::Sent)
        {        
            SessionMessages::add(__('sms:sent_ok'));
        }
        else
        {
            SessionMessages::add(__('sms:queued_ok'));
        }
        $this->redirect();
    }
}    