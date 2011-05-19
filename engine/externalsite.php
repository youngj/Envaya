<?php

class ExternalSite extends Entity
{
    static $table_name = 'external_sites';
    static $table_attributes = array(
        'subtype_id' => '',
        'url' => '',
        'title' => '',
    );
    
    function update()
    {
        
    }
}