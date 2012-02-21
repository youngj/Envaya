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
            'regex' => '/sms\b', 
            'controller' => 'Controller_SMSTemplate',
        ),
        array(
            'regex' => '/(?P<action>\w+)\b', 
        ),                
    );
    
    function before()
    {
        Permission_UseAdminTools::require_any();
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
            'content' => view('admin/contact', array(
            
            ))
        ));
    }
}