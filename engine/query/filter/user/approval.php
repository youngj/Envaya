<?php

class Query_Filter_User_Approval extends Query_Filter_Select
{
    function get_options()
    {
        return array(
            1 => "Approved",
            0 => "Awaiting Approval",
            -1 => "Rejected",
        );
    }

    static function get_empty_option()
    {
        return "Any status";
    }        
    
    static function get_name()
    {
        return "Status";
    }       
        
    function _apply($query)
    {
        return $query->where('approval = ?', $this->value);
    }
}