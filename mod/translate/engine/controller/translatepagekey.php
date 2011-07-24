<?php

class Controller_TranslatePageKey extends Controller_TranslateKey
{
    function index_page_draw($args)
    {
        $key = $this->param('key');
        $language = $this->param('language');
        $page_uri = $this->param('page_uri');
        
        return $this->page_draw(array_merge($args, array(
            'header' => view('translate/page_header', array(
                'items' => array(
                    array('url' => $page_uri, 'title' => $page_uri),
                    array('url' => $this->get_parent_uri(), 'title' => $language->name),
                    array('title' => $key->name)
                ),
            )),            
        ))); 
    }        
    
    function get_available_keys()
    {
        return $this->param('keys');
    }
}