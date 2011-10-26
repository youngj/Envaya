<?php

abstract class Controller_ContactTemplate extends Controller
{
    static $routes = array(
        array(
            'regex' => '(/)?$', 
            'action' => 'action_index',
        ),      
        array(
            'regex' => '/(?P<action>add|filters_count)\b', 
        ),       
        array(
            'regex' => '/subscription/(?P<subscription_guid>\d+)', 
            'action' => 'action_subscription',
            'before' => 'init_subscription',
        ),   
        array(
            'regex' => '/(?P<template_guid>\d+)(/(?P<action>\w+))?', 
            'defaults' => array('action' => 'view'),
            'before' => 'init_template',
        ),   
    );
    
    function before()
    {
        $this->require_admin();
        $this->page_draw_vars['theme_name'] = 'editor';
    }    
    
    function init_template()
    {
        $guid = $this->param('template_guid');
    
        $template_class = $this->template_class;    
        $template = $template_class::get_by_guid($guid);
        
        if ($template == null)
            throw new NotFoundException();
    
        $this->params['template'] = $template;
    }    
    
    function get_template()
    {
        return $this->param('template');
    }
    
    function get_header($vars)
    {
        $vars['index_url'] = $this->index_url;
        $vars['index_title'] = sprintf(__('contact:template_list'), $this->get_type_name());
        
        return view('admin/template_header', $vars);
    }
    
    function get_subscription_class()
    {
        $template_class = $this->template_class;
        return $template_class::$subscription_class;
    }

    function get_outgoing_message_class()
    {
        $template_class = $this->template_class;
        return $template_class::$outgoing_message_class;
    }
    
    function init_subscription()
    {
        $guid = $this->param('subscription_guid');
        
        $subscription_class = $this->get_subscription_class();
    
        $subscription = $subscription_class::get_by_guid($guid);
        
        if ($subscription == null)
            throw new NotFoundException();
    
        $this->params['subscription'] = $subscription;
    }      
    
    function action_index()
    {
        $this->page_draw(array(
            'title' => sprintf(__('contact:template_list'), $this->get_type_name()),
            'content' => view($this->index_view)
        ));        
    }  

    function action_subscription()
    {
        PageContext::get_submenu('edit')->add_item(__('cancel'), get_input('from') ?: "/admin/contact");
    
        $this->page_draw(array(
            'title' => sprintf(__('contact:template_list'), $this->get_type_name()),
            'content' => view($this->subscription_view, array('subscription' => $this->param('subscription')))
        ));        
    }  
        
    function action_view()
    {
        $template = $this->get_template();
        
        PageContext::get_submenu('edit')->add_item(
            sprintf(__('edit_item'), $this->get_type_name()),
            $template->get_url() . "/edit");
        
        $this->page_draw(array(
            'title' => sprintf(__('contact:view_template'), $this->get_type_name()),
            'header' => $this->get_header(array(
                'template' => $template,
            )),
            'content' => view($this->view_view, array(
                'template' => $template, 
                'from' => get_input('from')
            ))
        ));                    
    }                
    
    function action_filters_count()
    {
        $this->set_content_type('text/javascript');
        
        $filters_json = get_input('filters_json');
        $filters = Query_Filter::json_decode_filters($filters_json);
        
        $template_class = $this->template_class;
        
        $filter_count = $template_class::query_subscriptions($filters)->count();     
        
        $this->set_content(json_encode(array(
            'filter_count' => $filter_count
        )));
    }   
        
    function action_edit()
    {
        $edit_action = $this->edit_action;
        $action = new $edit_action($this);
        $action->execute();    
    }
   
    function action_add()
    {
        $add_action = $this->add_action;
        $action = new $add_action($this);
        $action->execute();               
    }

    function action_send()
    {
        $action = new Action_ContactTemplate_Send($this);
        $action->execute();
    }
    
    function action_reset_outgoing()
    {
        $action = new Action_ContactTemplate_ResetOutgoing($this);
        $action->execute();
    }
}