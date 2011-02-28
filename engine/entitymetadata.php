<?php

/*
 * An item of metadata for an Entity.
 *
 * e.g. $entity->foo = 7 will correspond to an instance of EntityMetadata
 * with name = foo, value = 7, and entity_guid = $entity->guid.
 *
 * value_type corresponds to a VariantType constant.
 */

class EntityMetadata extends Model
{
    static $table_name = 'metadata';
    static $table_attributes = array(
        'name' => '',
        'value' => 0,
        'value_type' => 0,
        'entity_guid' => 0
    );
    
    public $dirty = false;

    function get($name)
    {
        $value = parent::get($name);

        if ($name == 'value')
        {
            return VariantType::decode_value($value, $this->attributes['value_type']);
        }
        return $value;
    }

    function set($name, $value)
    {
        if ($name == 'value')
        {          
            $value = VariantType::encode_value($value, $this->attributes['value_type']);
        }
        parent::set($name, $value);
    }         
}