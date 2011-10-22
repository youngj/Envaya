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
            'regex' => '/subscription/(?P<subscription_guid>\d+)', 
            'action' => 'action_subscription',
            'before' => 'init_subscription',
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

    function init_subscription()
    {
        $guid = $this->param('subscription_guid');
    
        $subscription = EmailSubscription::get_by_guid($guid);
        
        if ($subscription == null)
            throw new NotFoundException();
    
        $this->params['subscription'] = $subscription;
    }
    
    function action_index()
    {
        $this->page_draw(array(
            'title' => __('contact:email_list'),
            'content' => view('admin/list_emails')
        ));        
    }  

    function action_subscription()
    {
        PageContext::get_submenu('edit')->add_item(__('cancel'), get_input('from') ?: "/admin/contact");
    
        $this->page_draw(array(
            'title' => __('contact:email_list'),
            'content' => view('admin/subscription_emails', array('subscription' => $this->param('subscription')))
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
        $subscription = EmailSubscription_Contact::get_by_guid(get_input('subscription'));
        if (!$subscription)
        {
            $subscription = new EmailSubscription_Contact();
            $subscription->name = '{name}';
            $subscription->email = '{email}';
        }        
        
        $email = $this->get_email();
                
        $this->set_content($subscription->render_html_body($email->render_content($subscription)));
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