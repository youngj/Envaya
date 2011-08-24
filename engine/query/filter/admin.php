<?php

class Query_Filter_Admin extends Query_Filter_Select
{
    function get_options()
    {
        return array(
            0 => "Non-admin",
            1 => "Admin",
        );
    }

    static function get_empty_option()
    {
        return "All roles";
    }        
    
    static function get_name()
    {
        return "Role";
    }       
        
    function _apply($query)
    {
        return $query->where('admin = ?', $this->value);
    }
}