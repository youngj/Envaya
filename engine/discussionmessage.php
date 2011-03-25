<?php

class DiscussionMessage extends Entity
{
    static $table_name = 'discussion_messages';
    
    static $table_attributes = array(
        'message_id' => '',
        'list_guid' => 0,
        'subject' => '',        
        'from_name' => '',
        'from_email' => '',
        'time_posted' => 0,
        
        'content' => '',
        'data_types' => 0,        
        'language' => '',
    );    
}