<?php

class Query_Filter_User_Region extends Query_Filter_Select
{    
    public $country;

    static function get_name()
    {
        return __('org:region');
    }
    
    function is_valid()
    {
        return !!$this->country;
    }
    
    function get_options()
    {
        return Geography::get_region_options($this->country);
    }

    static function get_all_text()
    {
        return __('region:all');
    }    
    
    static function get_empty_option()
    {
        return __('region:empty_option');
    }    
    
    function _apply($query)
    {
        return $query->with_region($this->value);
    }
    
    static function new_from_input()
    {
        return new Query_Filter_User_Region(array(
            'value' => get_input('region'),
            'country' => get_input('country')
        ));
    }        
}