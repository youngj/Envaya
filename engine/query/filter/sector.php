<?php

class Query_Filter_Sector extends Query_Filter_Select
{
    static function get_name()
    {
        return __('org:sector');
    }
    
    function get_options()
    {
        return OrgSectors::get_options();
    }

    static function get_all_text()
    {
        return __('sector:all');
    }        
    
    static function get_empty_option()
    {
        return __('sector:empty_option');
    }        
    
    function _apply($query)
    {
        return $query->with_sector((int)$this->value);
    }
}