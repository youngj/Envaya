<?php

class Controller_TranslateGroupKey extends Controller_TranslateKey
{      
    function action_index()
    {
        $key = $this->param('key');
        $group = $this->param('group');
        $language = $this->param('language');
        
        $key->init_defined_translation(true);
        
        return $this->page_draw(array(
            'title' => __('itrans:translations'),
            'header' => view('translate/header', array('items' => array(
                $language, 
                array('title' => $group->get_title(), 'url' => $this->get_parent_uri()), 
                $key
            ))),
            'content' => view('translate/interface_key', array(
                'key' => $key
            ))
        ));       
    }        
    
    function get_available_keys()
    {
        $group = $this->param('group');        
        return $group->get_available_keys();
    }    
}