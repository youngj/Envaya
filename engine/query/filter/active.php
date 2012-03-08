<?php

class Query_Filter_Active extends Query_Filter_Date
{    
    static function get_name()
    {
        return "Active Since";
    }
    
    static function get_param_name()
    {
        return 'active';
    }
    
    function apply_time($query, $time)
    {
        return $query->where('last_action >= ?', $time);
    }            
}