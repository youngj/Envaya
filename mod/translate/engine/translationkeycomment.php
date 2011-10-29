<?php

class TranslationKeyComment extends Entity
{
    static $table_name = 'translation_key_comments';
    static $table_attributes = array(
        'key_name' => '',
        'language_guid' => 0,
    );   
    static $mixin_classes = array(
        'Mixin_Content'
    );  

    function get_owner_name()
    {
        $owner = $this->get_owner_entity();
        return !$owner ? __('itrans:admin') : $owner->username;    
    }
    
    function get_key_in_language($language)
    {
        if ($language->guid == $this->language_guid)
        {
            return $this->get_container_entity();
        }
        else
        {
            return $language->query_keys()->where('name = ?', $this->key_name)->get();
        }
    }
}