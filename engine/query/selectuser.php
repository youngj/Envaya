<?php

/*
 * Represents a select query for an User subclass.
 */
class Query_SelectUser extends Query_SelectEntity
{
    function where_visible_to_user()
    {        
        if (!Session::isadminloggedin())
        {
            $this->where("(approval > 0 || e.guid = ?)", (int)Session::get_loggedin_userid());
        }
    }
    
    function with_sector($sector)
    {
        $this->join("INNER JOIN org_sectors s ON s.container_guid = e.guid");
        $this->where("s.sector_id=?", $sector);
    }
    
    function in_area($latMin, $longMin, $latMax, $longMax)
    {
        $this->where("latitude >= ?", $latMin);
        $this->where("latitude <= ?", $latMax);
        $this->where("longitude >= ?", $longMin);
        $this->where("longitude <= ?", $longMax);    
    }
}