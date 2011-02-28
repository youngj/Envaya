<?php

/*
 * Provides functions for storing and retrieving values of multiple types 
 * in a single database field. (e.g. metadata values)
 *
 * An alternative to this would be storing all variant types as JSON,
 * but this makes it slightly more tedious to run some SQL queries directly.
 */
class VariantType
{
    const _Integer = 1;
    const _Text = 2;
    const _JSON = 3;
    const _Bool = 4;

    static function detect_value_type($value)
    {
        if (is_array($value))
            return VariantType::_JSON;
        if (is_bool($value))
            return VariantType::_Bool;
        if (is_int($value))
            return VariantType::_Integer;
        if (is_numeric($value))
            return VariantType::_Text; // todo?
        return VariantType::_Text;
    }         

    static function decode_value($value, $value_type)
    {
        switch ($value_type)
        {
            case VariantType::_JSON:    return json_decode($value, true);
            case VariantType::_Integer: return (int)$value;
            case VariantType::_Bool:    return (bool)$value;
            default: return $value;                
        }
    }
    
    static function encode_value($value, &$value_type)
    {
        $value_type = static::detect_value_type($value);
        
        switch ($value_type)
        {   
            case VariantType::_JSON: return json_encode($value);
            case VariantType::_Bool: return $value ? 1 : 0;
            default: return $value;
        }            
    }
}