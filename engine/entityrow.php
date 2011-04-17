<?php

/*
 * A row of the 'entities' table, containing partial data for an Entity instance.
 *
 * Usually this is only used when manipulating many rows of the entities table at once.
 */
class EntityRow extends Model
{
    static $table_name = 'entities';
    static $primary_key = 'guid';
    
    static $table_attributes = array(
        'subtype' => 0,
    );   
    
    function save()
    {
        parent::save();        
        get_cache()->delete(Entity::entity_cache_key($this->guid));
    }
}