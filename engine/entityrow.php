<?php

class EntityRow extends Model
{
    static $table_name = 'entities';
    static $primary_key = 'guid';
    
    static $table_attributes = array(
        'type' => "object",
        'subtype' => 0,
        'owner_guid' => 0,
        'container_guid' => 0,
        'site_guid' => 0,
        'time_created' => 0,
        'time_updated' => 0,
        'enabled' => "yes",
    );   
    
    function save()
    {
        parent::save();        
        get_cache()->delete(Entity::entity_cache_key($this->guid));
    }
}