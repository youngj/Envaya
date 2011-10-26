<?php

class Action_ChangeBaseLanguage extends Action
{
    function before()
    {
        $this->require_editor($this->param('key'));        
    }

    function process_input()
    {    
        $key = $this->param('key');
        
        $lang = get_input('base_lang');
        
        if ($lang != '' && !Language::get($lang))
        {
            throw new ValidationException('Invalid language');
        }
        
        $entity = $key->get_container_entity();
        $entity->language = $lang;
        $entity->save();
        
        $this->redirect($this->get_parent_controller()->get_matched_uri());
    }
    
    function render()
    {
        return $this->index_page_draw(array(
            'content' => view('translate/base_lang', array(
                'key' => $this->param('key'),
            ))
        ));
    }
}