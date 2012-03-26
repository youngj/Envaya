<?php

class Controller_TranslateEntityKey extends Controller_TranslateKey
{
    function index_page_draw($args)
    {   
        $key = $this->param('key');
        $language = $this->param('language');        
        
        return $this->page_draw(array_merge($args, array(
            'header' => view('translate/header', array('items' => array(
                $language, 
                array(
                    'url' => $this->get_parent_uri(),
                    'title' => __('itrans:user_content')
                ),
                $key
            ))),
        )));       
    }

    function get_delta_key($key, $delta)
    {
        return $this->get_delta_key_from_query($key, $delta, 
            EntityTranslationKey::query(), 'time_updated');
    }   
}