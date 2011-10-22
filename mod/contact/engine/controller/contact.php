<?php

class Controller_Contact extends Controller
{
    static $routes = array(
        array(
            'regex' => '(/)?$', 
            'action' => 'action_index',
        ),        
        array(
            'regex' => '/email\b', 
            'controller' => 'Controller_EmailTemplate',
        ),        
        array(
            'regex' => '/(?P<action>\w+)\b', 
        ),                
    );
    
    function before()
    {
        $this->require_admin();
    }

    function action_filters_count()
    {
        $this->set_content_type('text/javascript');
        
        $filters_json = get_input('filters_json');
        $filters = Query_Filter::json_decode_filters($filters_json);
        
        $filter_count = EmailTemplate::query_subscriptions($filters)->count();     
        
        $this->set_content(json_encode(array(
            'filter_count' => $filter_count
        )));    
    }   
    
    function action_filter_input()
    {
        $this->set_content_type('text/javascript');
        $subclass = get_input('subclass');         
        $id = get_input('id');         
        $cls = "Query_Filter_{$subclass}";
        $filter = new $cls();
        $this->set_content(json_encode(array(
            'name' => $cls::get_name(),
            'input_html' => $filter->render_input(array(
                'id' => $id,
                'empty_option' => false,                
            ))
        )));
    }
    
    function action_index()
    {
        $this->page_draw(array(
            'theme_name' => 'simple_wide',
            'title' => __('contact:user_list'),
            'header' => '',
            'content' => view('admin/contact')
        ));
    }
}