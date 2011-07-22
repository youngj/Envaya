<?php

class Controller_TranslatePageKey extends Controller_TranslateKey
{
    function index_page_draw($args)
    {
        $key = $this->param('key');
        $language = $this->param('language');
        
        return $this->page_draw(array_merge($args, array(
            'header' => view('translate/header', array('items' => array(
                $language, 
                array(
                    'title' => $this->param('page_uri'),
                    'url' => $this->get_parent_uri()
                ),
                $key
            )))
        ))); 
    }        
    
    function get_available_keys()
    {
        return $this->param('keys');
    }
}