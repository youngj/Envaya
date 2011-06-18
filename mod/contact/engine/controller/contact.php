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
        
    );
    
    function before()
    {
        $this->require_admin();
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