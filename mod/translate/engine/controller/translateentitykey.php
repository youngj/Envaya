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
                array('url' => "/tr/{$language->code}/content", 'title' => __('itrans:user_content')),
                $key
            ))),
        )));       
    }       

    function redirect_delta($delta)
    {
        $parent_uri = $this->get_parent_uri();
        $key = $this->param('key');
        
        if ($delta > 0)
        {
            $cmp = '<';
            $dir = 'desc';
        }
        else
        {
            $cmp = '>';
            $dir = 'asc';        
        }
        
        $next_key = EntityTranslationKey::query()            
            ->where('language_guid = ?', $this->param('language')->guid)
            ->where("time_updated $cmp ? or (time_updated = ? AND guid $cmp ?)", 
                $key->time_updated, $key->time_updated, $key->guid)
            ->order_by("time_updated $dir, guid $dir")
            ->get();
        
        if ($next_key)
        {
            return $this->redirect($parent_uri . "/" . urlencode_alpha($next_key->name));
        }
        
        return $this->redirect($parent_uri);
    }        
}