<?php

class Action_ContactTemplate_ResetOutgoing extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $template = $this->get_template();
        
        $id = (int)get_input('id');
        
        $outgoing_message_class = $this->get_outgoing_message_class();
        
        $outgoing_message = $outgoing_message_class::query()->where('id = ?', $id)->get();
        if (!$outgoing_message)
        {
            throw new NotFoundException();
        }
        
        $outgoing_message->notifier_guid = 0;
        $outgoing_message->save();
        
        $template->update();
        
        $this->redirect();
    }
}    