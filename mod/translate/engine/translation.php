<?php

/*
 * Represents a user-contributed translation (or correction to existing translation)
 * of a piece of text from Envaya's user interface or user-generated content 
 * into a particular language.
 *
 * For Translations to be used in Envaya's user interface, first they must be 
 * exported and saved as PHP files in languages/ , 
 * unless Config::get('translate:live_interface') is true.
 */
class Translation extends Entity
{
    static $table_name = 'translation_strings';
    static $table_attributes = array(
        'language_guid' => 0,
        'value' => '',
        'default_value_hash' => '', // sha1 of the key's default value when this translation was created; 
                                    // allows detecting stale translations
        'score' => 0,
        'approval' => 0,
        'approval_time' => 0,
    );   
    
    function is_stale()
    {    
        $key = $this->get_container_entity();        
        return $this->default_value_hash != sha1($key->get_default_value());
    }
    
    function is_approved()
    {
        return $this->approval > 0;
    }
    
    function set_approved($approved)
    {
        if ($approved)
        {
            $this->approval = 1;
            $this->approval_time = time();
        }
        else
        {
            $this->approval = 0;
        }
    }
    
    function update($recursive = false)
    {
        $row = $this->query_votes()
            ->columns("sum(score) as score")
            ->set_row_function(null)
            ->get();
    
        $this->score = $row->score;
        $this->save();
        if ($recursive)
        {
            $this->get_container_entity()->update($recursive);
        }
    }    
    
    function update_default_value()
    {
        $key = $this->get_container_entity();
        $this->default_value_hash = sha1($key->get_default_value());
    }
    
    function save()
    {
        if (!$this->default_value_hash)
        {
            $this->update_default_value();
        }
    
        $key = $this->get_container_entity();
        if (!$this->language_guid)
        {
            $this->language_guid = $key->language_guid;
        }
        parent::save();
    }
    
    function get_language()
    {
        return TranslationLanguage::get_by_guid($this->language_guid);
    }
    
    function get_url()
    {
        return $this->get_container_entity()->get_url() . "/{$this->guid}";
    }
    
    function query_votes()
    {
        return TranslationVote::query()->where('container_guid = ?', $this->guid);
    }
    
    function get_owner_link()
    {
        $owner = $this->get_owner_entity();
        if ($owner)
        {
            $language = $this->get_language();
            return "<a href='{$language->get_url()}/translators/{$owner->guid}'>".escape($owner->username)."</a>";
        }
        else
        {
            return __('itrans:admin');
        }    
    }
}
