<?php

class Controller_TranslateAdmin extends Controller
{
    static $routes = array(
        array(
            'regex' => '(/)?$', 
            'defaults' => array('action' => 'index'), 
        ),    
        array(
            'regex' => '/(?P<lang>\w+)(/)?$', 
            'defaults' => array('action' => 'manage_lang'), 
        ),        
    );
    
    function before()
    {
        $this->require_admin();
        $this->page_draw_vars['theme_name'] = 'editor';
    }
    
    function action_index()
    {
        return $this->page_draw(array(
            'title' => __('itrans:manage'),
            'header' => view('translate/admin/header'),
            'content' => view('translate/admin/index')
        ));
    }
    
    function action_manage_lang()
    {
        $action = new Action_Admin_ManageLanguage($this);
        $action->execute();
    }
}