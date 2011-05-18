<?php

class Action_Admin_AddEmailTemplate extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {
        $content = get_input('content');
        
        $email = new EmailTemplate();
        $email->from = get_input('from');
        $email->subject = get_input('subject');        
        $email->set_content($content);
        $email->save();
        $this->redirect("/admin/view_email?email={$email->guid}");
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __('email:add'),
            'content' => view('admin/add_email'),
        ));     
    }    
}    