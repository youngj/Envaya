<?php

class Query_Filter_Widget_Sector extends Query_Filter_User_Sector
{
    function _apply($query)
    {
        return $query->where('exists (select id from org_sectors where container_guid = widgets.user_guid and sector_id = ?)', (int)$this->value);
    }
}