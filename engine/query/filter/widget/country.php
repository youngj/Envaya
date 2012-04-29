<?php

class Query_Filter_Widget_Country extends Query_Filter_User_Country
{    
    function _apply($query)
    {
        $query->join_users();
        return $query->where('u.country = ?', $this->value);
    }
}