<?php

class Action_EmailTemplate_Edit extends Action
{       
    function before()
    {
        $this->require_admin();
    }
 
    protected function save_draft()
    {
        $this->set_content_type('text/javascript');
    
        validate_security_token();
            
        $email = $this->get_email();
            
        $content = get_input('content');                       
            
        $revision = ContentRevision::get_recent_draft($email);
        $revision->time_updated = time();
        $revision->content = $content;
        $revision->save();
        
        $email->set_content($content);
        $email->save();
        
        $this->set_response(json_encode(array('guid' => $email->guid)));    
    }
 
    function process_input()
    {
        $email = $this->get_email();
        
        if (get_input('_draft'))
        {
            $this->save_draft();        
        }        
        else if (get_input('delete'))
        {
            $email->disable();
            $email->save();
            $this->redirect("/admin/contact/email");
        }
        else
        {
            $email->subject = get_input('subject');                
            $email->set_content(get_input('content'));
            $email->from = get_input('from');
            $email->save();
            $this->redirect($email->get_url());       
        }
    }

    function render()
    {
        $email = $this->get_email();
    
        PageContext::get_submenu('edit')->add_item(__('canceledit'), get_input('from') ?: $email->get_url());
    
        $this->page_draw(array(
            'title' => __('contact:edit_email'),
            'header' => view('admin/email_header', array(
                'email' => $email,
                'title' => __('edit')
            )),
            'content' => view('admin/edit_email', array('email' => $email)),
        ));
    }    
}    