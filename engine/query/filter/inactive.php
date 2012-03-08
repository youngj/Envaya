<?php

class Query_Filter_Inactive extends Query_Filter_Date
{    
    static function get_name()
    {
        return "Inactive Since";
    }
    
    static function get_param_name()
    {
        return 'inactive';
    }
    
    function apply_time($query, $time)
    {       
        return $query->where('last_action < ?', $time);
    }            
}