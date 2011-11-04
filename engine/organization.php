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
        $this->set_design_setting('theme_name', "green");
        $this->set_design_setting('share_links', array('email','facebook','twitter'));    
    }
    
    public function get_continue_setup_url()
    {
        return "/org/new?step={$this->setup_state}";
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

    protected $attributes_dirty = null;
    
    function __set($name, $value)
    {
        parent::__set($name,$value);
        
        if (!$this->attributes_dirty)
        {
            $this->attributes_dirty = array();
        }
        $this->attributes_dirty[$name] = true;
    }
    
    public function save()
    {
        $isNew = !$this->guid;
    
        $res = parent::save();
        
        $attributesDirty = $this->attributes_dirty ?: array();
        
        $this->attributes_dirty = false;
        
        $sectorsDirty = $this->sectors_dirty;
        if ($sectorsDirty)
        {
            Database::delete("delete from org_sectors where container_guid = ?", array($this->guid));
            foreach ($this->sectors as $sector)
            {
                Database::update("insert into org_sectors (container_guid, sector_id) VALUES (?,?)", array($this->guid, $sector));
            }
            $this->sectors_dirty = false;
        }
                        
        if ($isNew || $sectorsDirty 
            || @$attributesDirty['name'] || @$attributesDirty['username'] || @$attributesDirty['region'])
        {
            Sphinx::reindex();
        }        
        
        return $res;
    }             
}