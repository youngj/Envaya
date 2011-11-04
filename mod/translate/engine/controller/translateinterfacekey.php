<?php

class Controller_TranslateInterfaceKey extends Controller_TranslateKey
{      
    function index_page_draw($args)
    {
        $key = $this->param('key');
        $language = $this->param('language');
        
        $key->init_defined_translation(true);
        
        return $this->page_draw(array_merge($args, array(
            'header' => view('translate/header', array('items' => array(
                $language, 
                array('title' => __('itrans:interface_translations'), 'url' => $this->get_parent_uri()), 
                $key
            ))),
        )));       
    }        
    
    function get_delta_key($key, $delta)
    {
        return $this->get_delta_key_from_query($key, $delta, 
            InterfaceKey::query(), 'time_updated');
    }
}