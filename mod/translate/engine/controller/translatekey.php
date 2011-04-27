<?php

class Controller_TranslateKey extends Controller
{
    static $routes = array(
        array(
            'regex' => '(/)?$', 
        ),
        array(
            'regex' => '/(?P<translation_guid>\d+)/delete\b', 
            'defaults' => array('action' => 'delete_translation'),
        ),                
        array(
            'regex' => '/(?P<action>\w+)\b', 
        ),        
    );    
    
    function before()
    {
        $this->page_draw_vars['theme_name'] = 'simple_wide';
    }    
    
    function action_index()
    {
        $key = $this->param('key');
        $group = $this->param('group');
        $language = $this->param('language');
        
        $key->init_defined_translation(true);
        
        return $this->page_draw(array(
            'title' => __('itrans:title'),
            'header' => view('translate/header', array('items' => array($language, $group, $key))),
            'content' => view('translate/interface_key', array('key' => $key))
        ));       
    }        
    
    function action_add()
    {
        $action = new Action_AddInterfaceTranslation($this);
        $action->execute();
    }
    
    function action_delete_translation()
    {
        $action = new Action_DeleteInterfaceTranslation($this);
        $action->execute();        
    }    
}