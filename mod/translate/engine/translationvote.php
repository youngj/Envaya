<?php

/*
 * Represents a user's score (+1 = upvote,-1 = downvote) for a translation.
 */
class TranslationVote extends Entity
{
    static $table_name = 'translation_votes';
    static $table_attributes = array(
        'score' => 0,
        'language_guid' => 0,
    );   
    
    function save()
    {
        if (!$this->language_guid)
        {
            $translation = $this->get_container_entity();
            $this->language_guid = $translation->language_guid;
        }
        parent::save();
    }    
}
