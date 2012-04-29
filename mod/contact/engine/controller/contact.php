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

    function action_filter_input()
    {
        Permission_UseAdminTools::require_any();
    
        $this->set_content_type('text/javascript');
        $subtype_id = get_input('subtype_id');         
        $id = get_input('id');         
        $cls = ClassRegistry::get_class($subtype_id);
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
        Permission_UseAdminTools::require_any();
    
        $this->page_draw(array(
            'theme' => 'Theme_Wide',
            'title' => __('contact:user_list'),
            'header' => '',
            'content' => view('admin/contact', array(
            
            ))
        ));
    }
}