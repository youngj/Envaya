<?php

/*
 * A civil society organization that has registered for Envaya.
 * Each organization is its own user account.
 */
class Organization extends User
{
    // event names
    const Registered = 'org_registered';

    function set_defaults()
    {
        $this->set_design_setting('theme_id', Theme_LightBlue::get_subtype_id());
    }
    
    public function get_continue_setup_url()
    {
        return "/org/create_profile";
    }        
    
    function init_default_widgets()
    {
        /* auto-create empty pages */
        $home = Widget_Home::get_or_init_for_entity($this);
        Widget_Mission::get_or_init_for_entity($home);
        Widget_Updates::get_or_init_for_entity($home);
        Widget_Sectors::get_or_init_for_entity($home);
        Widget_Location::get_or_init_for_entity($home);                
        Widget_News::get_or_init_for_entity($this);
        Widget_Contact::get_or_init_for_entity($this);
    }
            
    public function get_feed_names()
    {
        $feedNames = parent::get_feed_names();

        if ($this->region)
        {
            $feedNames[] = FeedItem::make_feed_name(array("region" => $this->region));
        }
        
        if ($this->country)
        {
            $feedNames[] = FeedItem::make_feed_name(array("country" => $this->country));
        }

        foreach ($this->get_sectors() as $sector)
        {
            $feedNames[] = FeedItem::make_feed_name(array('sector' => $sector));

            if ($this->region)
            {
                $feedNames[] = FeedItem::make_feed_name(array('region' => $this->region, 'sector' => $sector));
            }
            
            if ($this->country)
            {
                $feedNames[] = FeedItem::make_feed_name(array('country' => $this->country, 'sector' => $sector));
            }
        }
        
        /*
        foreach ($this->query_subject_relationships()->filter() as $relationship)
        {
            $feedNames[] = FeedItem::make_feed_name(array('network' => $relationship->container_guid));
        }        
        */

        return $feedNames;
    }
        
    protected $sectors;
    protected $sectors_dirty = false;

    public function get_sectors()
    {
        if (!isset($this->sectors))
        {
            $sectorRows = Database::get_rows("select * from org_sectors where container_guid = ?", array($this->guid));
            $sectors = array();
            foreach ($sectorRows as $row)
            {
                $sectors[] = $row->sector_id;
            }
            $this->sectors = $sectors;
        }
        return $this->sectors;
    }

    public function set_sectors($arr)
    {
        $this->sectors = $arr;
        $this->sectors_dirty = true;
    }

    public function save()
    {
        $isNew = !$this->guid;
    
		$dirty_attributes = $this->dirty_attributes;
		
		$sectorsDirty = $this->sectors_dirty;
		
		$needs_reindex = $isNew || $sectorsDirty 
			|| isset($dirty_attributes['name']) 
			|| isset($dirty_attributes['username']) 
			|| isset($dirty_attributes['region']);	
	
        $res = parent::save();
        
        if ($sectorsDirty)
        {
            Database::delete("delete from org_sectors where container_guid = ?", array($this->guid));
            foreach ($this->sectors as $sector)
            {
                Database::update("insert into org_sectors (container_guid, sector_id) VALUES (?,?)", array($this->guid, $sector));
            }
            $this->sectors_dirty = false;
        }
                        
        if ($needs_reindex)
        {
            Sphinx::reindex();
        }        
        
        return $res;
    }             
}