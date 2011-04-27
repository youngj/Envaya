<?php

/*
 * Represents a user's score (+1 = upvote,-1 = downvote) for a translation.
 */
class TranslationVote extends Entity
{
    static $table_name = 'translation_votes';
    static $table_attributes = array(
        'score' => 0,
    );   
}
