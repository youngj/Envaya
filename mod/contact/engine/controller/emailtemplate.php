<?php

class Controller_EmailTemplate extends Controller_ContactTemplate
{
    public $template_class = 'EmailTemplate';    
    public $index_url = '/admin/contact/email';
    public $index_view = 'admin/list_email_templates';
    public $subscription_view ='admin/subscription_email_templates';
    public $view_view = 'admin/view_email_template';
    public $edit_view = 'admin/edit_email_template';
    public $send_view = 'admin/send_email_template';
    public $add_view = 'admin/add_email_template';
    public $add_action = 'Action_EmailTemplate_Add';
    public $edit_action = 'Action_EmailTemplate_Edit';
    
    function get_type_name()
    {
        return __('contact:email');
    }

    function action_preview_body()
    {    
        $subscription = EmailSubscription_Contact::get_by_guid(Input::get_string('subscription'));
        if (!$subscription)
        {
            $subscription = new EmailSubscription_Contact();
            $subscription->name = '{{name}}';
            $subscription->email = '{{email}}';
        }        
        
        $template = $this->get_template();                
        $this->set_content($subscription->render_html_body($template->render_content($subscription)));
    }    
    
}