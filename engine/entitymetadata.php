<?php

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