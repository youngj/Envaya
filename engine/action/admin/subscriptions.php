<?php

class Action_Admin_Subscriptions extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {        
        $email = get_input('email');        
        if ($email)
        {
            $this->redirect(EmailSubscription::get_all_settings_url($email));
        }
        else
        {
            $this->render();
        }
    }
    
    function render()
    {
        $this->page_draw(array(
            'title' => 'Manage Subscriptions',
            'content' => view('admin/subscriptions'),
            'theme_name' => 'editor',
        ));                     
    }
}    