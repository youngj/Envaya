<?php

class Query_Filter_Widget_Region extends Query_Filter_User_Region
{    
    function _apply($query)
    {
        $query->join_users();
        return $query->where('u.region = ?', $this->value);
    }
}