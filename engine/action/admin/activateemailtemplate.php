<?php

class Action_Admin_ActivateEmailTemplate extends Action
{
    protected $email;

    function before()
    {
        $this->require_admin();
    
        $email = EmailTemplate::get_by_guid(get_input('email'));
        if (!$email)
        {
            throw new NotFoundException();
        }
        $this->email = $email;        
    }
     
    function process_input()
    {        
        foreach (EmailTemplate::query()->where('active<>0')->filter() as $activeEmail)
        {
            $activeEmail->active = 0;
            $activeEmail->save();
        }

        $email = $this->email;
        $email->active = 1;            
        $email->save();
     
        SessionMessages::add('activated');
        $this->redirect('/admin/emails');        
    }
}    