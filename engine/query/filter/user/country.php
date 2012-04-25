<?php

class Query_Filter_User_Country extends Query_Filter_Select
{
    static function get_name()
    {
        return __('country');
    }
    
    function get_options()
    {
        return Geography::get_country_options();
    }
    
    static function get_all_text()
    {
        return __('country:all');
    }    
    
    static function get_empty_option()
    {
        return __('country:empty_option');
    }    
    
    function _apply($query)
    {
        return $query->with_country($this->value);
    }
}