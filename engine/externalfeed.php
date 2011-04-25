<?php

class ExternalFeed extends Entity
{
    static $table_name = 'external_feeds';
    static $table_attributes = array(
        'subtype_id' => '',
        'url' => '',
    );
    
    function update()
    {
        
    }
}