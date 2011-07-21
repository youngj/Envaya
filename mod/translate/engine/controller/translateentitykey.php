<?php

class Controller_TranslateEntityKey extends Controller_TranslateKey
{
    function action_index()
    {
        $key = $this->param('key');
        $language = $this->param('language');
        
        return $this->page_draw(array(
            'title' => __('itrans:translations'),
            'header' => view('translate/header', array('items' => array($language, $key))),
            'content' => view('translate/interface_key', array('key' => $key))
        ));       
    }        
}