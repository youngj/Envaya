<?php

class Action_Admin_EditEmailTemplate extends Action
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
        $email = $this->email;
        
        if (get_input('delete'))
        {
            $email->disable();
            $email->save();
            $this->redirect("/admin/emails");
        }
        else
        {
            $email->subject = get_input('subject');                
            $email->set_content(get_input('content'));
            $email->from = get_input('from');
            $email->save();
            $this->redirect("/admin/view_email?email={$email->guid}");       
        }
    }

    function render()
    {
        $email = $this->email;
    
        $this->page_draw(array(
            'title' => __('email:edit'),
            'content' => view('admin/edit_email', array('email' => $email)),
        ));
    }    
}    