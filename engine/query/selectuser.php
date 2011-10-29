<?php

/*
 * Represents a select query for an User subclass.
 */
class Query_SelectUser extends Query_SelectEntity
{
    protected $fulltext_query = null;
    protected $sector = null;
    protected $country = null;
    protected $region = null;

    function where_visible_to_user()
    {        
        if (!Permission_ViewUserSite::has_for_root())
        {
            $user = Session::get_logged_in_user();
            if ($user)
            {
                $this->where("(approval > 0 OR guid = ?)", $user->guid);
            }
            else
            {
                $this->where("approval > 0");
            }
        }
        return $this;
    }
    
    function with_sector($sector)
    {    
        $this->sector = $sector;        
        return $this;
    }
    
    function with_region($region)
    {       
        $this->region = $region;
        return $this;
    }
    
    function with_country($country)
    {
        $this->country = $country;
        return $this;
    }
    
    function in_area($latMin, $longMin, $latMax, $longMax)
    {
        $this->where("latitude >= ?", $latMin);
        $this->where("latitude <= ?", $latMax);
        $this->where("longitude >= ?", $longMin);
        $this->where("longitude <= ?", $longMax);    
        return $this;
    }

    function fulltext($name)
    {
        $q_words = preg_split('#\s+#', trim($name));
        
        // use sphinx prefix matching
        $q_words = array_map(function($word) { return "$word*"; }, $q_words);        
        $sphinx_q = implode(" ", $q_words);
    
        $this->fulltext_query = $sphinx_q;    		
        return $this;        
    }

    protected function finalize_query()
    {
        parent::finalize_query();
    
        if ($this->region)
        {
            $this->where('region = ?', $this->region);
        }
        
        if ($this->country)
        {
            $this->where('country = ?', $this->country);
        }    
    
        if (!$this->fulltext_query)
        {
            if ($this->sector)
            {
                $this->where("exists (select id from org_sectors s where s.container_guid = guid AND s.sector_id = ?)", $this->sector);        
            }            
        }
        else
        {
            $sphinx = Sphinx::get_client();
            $sphinx->setMatchMode(SPH_MATCH_ANY);
            $sphinx->setLimits(0,30);
            $sphinx->setConnectTimeout(5);
            $sphinx->setMaxQueryTime(3);
            
            if ($this->sector)
            {
                $sphinx->setFilter('sector_id', array($this->sector));
            }
            
            $results = $sphinx->query($this->fulltext_query, 'orgs');
            
            if (!$results)
            {
                throw new IOException("Error connecting to search service");
            }            
            
            $matches = @$results['matches'];
                        
            if (!is_array($matches) || sizeof($matches) == 0)
            {
                $this->is_empty = true; 
            }
            else
            {                   
                $user_guids = array_keys($matches);
                $sql_guids = implode(',',$user_guids);
             
                $this->where("guid in ($sql_guids)");
                $this->order_by("FIND_IN_SET(guid, '$sql_guids')", true);
            }
        }
    }       
}