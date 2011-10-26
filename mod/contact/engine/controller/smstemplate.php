<?php

class Controller_SMSTemplate extends Controller_ContactTemplate
{
    public $template_class = 'SMSTemplate';    
    public $index_url = '/admin/contact/sms';
    public $index_view = 'admin/list_sms_templates';
    public $subscription_view ='admin/subscription_sms_templates';
    public $view_view = 'admin/view_sms_template';
    public $edit_view = 'admin/edit_sms_template';
    public $send_view = 'admin/send_sms_template';
    public $add_view = 'admin/add_sms_template';
    public $add_action = 'Action_SMSTemplate_Add';
    public $edit_action = 'Action_SMSTemplate_Edit';
    
    function get_type_name()
    {
        return __('contact:sms');
    }    
}