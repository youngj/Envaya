<?php

class Controller_Contact extends Controller
{
    static $routes = array(
        array(
            'regex' => '(/)?$', 
            'defaults' => array('action' => 'index'), 
        ),        
        array(
            'regex' => '/email\b', 
            'defaults' => array('controller' => 'EmailTemplate'),
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