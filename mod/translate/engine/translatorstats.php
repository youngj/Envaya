<?php

/* 
 * Summary statistics for one user of the translation system (owner_guid) 
 * in a particular language (container_guid).
 */
class TranslatorStats extends Entity
{
    static $table_name = 'translator_stats';
    static $table_attributes = array(
        'num_translations' => 0,
        'score' => 0,
        'num_votes' => 0,
    );       
    
    function get_url()
    {   
        $language = $this->get_container_entity();
        $user = $this->get_owner_entity();
        return "/tr/{$language->code}/translators/{$this->owner_guid}";
    }
    
    function get_display_name()
    {
        $user = $this->get_owner_entity();
        return "{$user->name} ({$user->username})";
    }
    
    function update()
    {
        $language = $this->get_container_entity();
        
        $row = $language->query_translations()
            ->where('owner_guid = ?', $this->owner_guid)
            ->columns('count(*) as num_translations, sum(score) as score')
            ->set_row_function(null)
            ->get();
            
        $this->num_translations = $row->num_translations ?: 0;
        $this->score = $row->score ?: 0;        
        
        $this->num_votes = $language->query_votes()
            ->where('owner_guid = ?', $this->owner_guid)
            ->where('score <> 0')
            ->count();
    
        $this->save();
    }
}
