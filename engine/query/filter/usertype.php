<?php

class Query_Filter_UserType extends Query_Filter_Select
{
    static function get_name()
    {
        return "User Type";
    }
    
    function get_options()
    {
        return array(
            Organization::get_subtype_id() => "Organization",
            User::get_subtype_id() => "Regular User",                
        );
    }        
    
    static function get_empty_option()
    {
        return "All user types";
    }              
    
    function _apply($query)
    {
        return $query->where('subtype_id = ?', $this->value);
    }
}