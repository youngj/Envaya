<?php

class Controller_TranslatePageKey extends Controller_TranslateKey
{
    function action_index()
    {
        $key = $this->param('key');
        $language = $this->param('language');
        
        return $this->page_draw(array(
            'title' => __('itrans:translations'),
            'header' => view('translate/header', array('items' => array(
                $language, 
                array(
                    'title' => $this->param('page_uri'),
                    'url' => $this->get_parent_uri()
                ),
                $key
            ))),
            'content' => view('translate/interface_key', array(
                'key' => $key,                
            ))
        ));       
    }        
    
    function get_available_keys()
    {
        return $this->param('keys');
    }
}