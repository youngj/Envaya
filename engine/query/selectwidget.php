<?php

class Query_SelectWidget extends Query_SelectEntity
{
    function where_published($published = true)
    {    
        if ($published)
        {
            return $this->where('publish_status = ?', Widget::Published);
        }
        else
        {
            return $this->where('publish_status <> ?', Widget::Published);
        }        
    }
    
    function where_in_menu($in_menu = true)
    {
        return $this->where_published()->where('in_menu = 1');
    }
}