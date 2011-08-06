<?php

class Controller_EmailTemplate extends Controller
{
    static $routes = array(
        array(
            'regex' => '(/)?$', 
            'action' => 'action_index',
        ),      
        array(
            'regex' => '/(?P<action>add)\b', 
        ),       
        array(
            'regex' => '/user/(?P<user_guid>\d+)', 
            'action' => 'action_user',
            'before' => 'init_user',
        ),   
        array(
            'regex' => '/(?P<email_guid>\d+)(/(?P<action>\w+))?', 
            'defaults' => array('action' => 'view'),
            'before' => 'init_email',
        ),   
    );
    
    function before()
    {
        $this->require_admin();
        $this->page_draw_vars['theme_name'] = 'editor';
    }    
    
    function init_email()
    {
        $guid = $this->param('email_guid');
    
        $email = EmailTemplate::get_by_guid($guid);
        
        if ($email == null)
            throw new NotFoundException();
    
        $this->params['email'] = $email;
    }

    function init_user()
    {
        $guid = $this->param('user_guid');
    
        $user = User::get_by_guid($guid);
        
        if ($user == null)
            throw new NotFoundException();
    
        $this->params['user'] = $user;
    }
    
    function action_index()
    {
        $this->page_draw(array(
            'title' => __('contact:email_list'),
            'content' => view('admin/list_emails')
        ));        
    }  

    function action_user()
    {
        PageContext::get_submenu('edit')->add_item(__('cancel'), get_input('from') ?: "/admin/contact");
    
        $this->page_draw(array(
            'title' => __('contact:email_list'),
            'content' => view('admin/user_emails', array('user' => $this->param('user')))
        ));        
    }  
    
    function get_email()
    {
        return $this->param('email');
    }
    
    function action_view()
    {
        $user = User::get_by_username(get_input('username'));
       
        $email = $this->get_email();
        
        PageContext::get_submenu('edit')->add_item("Edit Email", $email->get_url() . "/edit");
        
        $this->page_draw(array(
            'title' => __('contact:view_email'),
            'header' => view('admin/email_header', array(
                'email' => $email,
            )),
            'content' => view('admin/view_email', array(
                'user' => $user, 
                'email' => $email, 
                'from' => get_input('from')
            ))
        ));                    
    }        
        
    function action_preview_body()
    {
        $user = User::get_by_guid(get_input('user'));
        
        $email = $this->get_email();
        
        echo view('emails/template', array(
            'user' => $user, 
            'base' => 'http://ERROR_RELATIVE_URL/ERROR_RELATIVE_URL/', 
            'email' => $email
        ));            
    }

   
    function action_edit()
    {
        $action = new Action_EmailTemplate_Edit($this);
        $action->execute();    
    }
   
    function action_add()
    {
        $action = new Action_EmailTemplate_Add($this);
        $action->execute();               
    }

    function action_send()
    {
        $action = new Action_EmailTemplate_Send($this);
        $action->execute();
    }
    
    function action_reset_outgoing()
    {
        $action = new Action_EmailTemplate_ResetOutgoingMail($this);
        $action->execute();
    }
}