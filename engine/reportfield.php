<?php

class ReportField extends Model
{
    static $table_name = 'report_fields';
    static $table_attributes = array(
        'name' => '',
        'value' => '',
        'value_type' => 0,
        'report_guid' => 0
    );   

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
        echo "$name = $value";
        var_dump($this);
    }         
    
    function view($args)
    {
        return escape($this->value);
    }
    
    function edit($args)
    {
        return view($args['input_type'], array(
            'internalname' => $this->name,
            'trackDirty' => true,
            'value' => $this->value ?: @$args['default']
        ));
    }    
}