<?php

/*
 * A civil society organization that has registered for Envaya.
 * Each organization is its own user account.
 */
class Organization extends User
{
    static $mixin_classes = array(
        'Mixin_WidgetContainer',
    );

    static function query()
    {
        $query = User::query();
        $query->where("subtype_id=?", static::get_subtype_id());
        return $query;
    }

    public function is_setup_complete()
    {
        return $this->setup_state >= SetupState::CreatedHomePage;
    }
    
    public function query_relationships()
    {
        return OrgRelationship::query()->where("container_guid=?", $this->guid)->order_by('subject_name asc');
    }       
        
    public function query_subject_relationships()
    {
        return OrgRelationship::query()->where("subject_guid=?", $this->guid);
    }

    public function query_external_sites()
    {
        return ExternalSite::query()->where('container_guid = ?', $this->guid)->order_by('`order`');
    }
    
    public function query_discussion_topics()
    {
        return DiscussionTopic::query()->where('container_guid = ?', $this->guid)->order_by('last_time_posted desc');
    }
    
    public function query_files()
    {    
        return UploadedFile::query()->where('container_guid=?',$this->guid);
    }
            
    public function get_feed_names()
    {
        $feedNames = parent::get_feed_names();

        if ($this->region)
        {
            $feedNames[] = FeedItem::make_feed_name(array("region" => $this->region));
        }

        foreach ($this->get_sectors() as $sector)
        {
            $feedNames[] = FeedItem::make_feed_name(array('sector' => $sector));

            if ($this->region)
            {
                $feedNames[] = FeedItem::make_feed_name(array('region' => $this->region, 'sector' => $sector));
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

    public function can_view()
    {
        return $this->approval > 0 || $this->can_edit();
    }
        
    public function get_country_text()
    {
        if ($this->country)
        {
            return __("country:{$this->country}");
        }
        else
        {
            return '';
        }
    }

    public function get_location_text($includeRegion = true)
    {
        $res = '';

        if ($this->city)
        {
            $res .= "{$this->city}, ";
        }
        if ($this->region && $includeRegion)
        {
            $regionText = __($this->region);

            if ($regionText != $this->city)
            {
                $res .= "$regionText, ";
            }
        }
        $res .= $this->get_country_text();

        return $res;
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
        
        if ($name == 'phone_number')
        {
            $this->set_phone_number($value);
        }
        
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
        
        if ($this->phone_numbers_dirty)
        {            
            $newIds = array_map(function($op) { return $op->id; }, $this->phone_numbers);        
            
            foreach ($this->query_phone_numbers()
                ->where('confirmed = 0')
                ->where_not_in('id', $newIds)
                ->filter() 
                    as $oldPhoneNumber)
            {
                $oldPhoneNumber->delete();
            }
            foreach ($this->phone_numbers as $phone_number)
            {
                $phone_number->org_guid = $this->guid;
                $phone_number->save();
            }        
            $this->phone_numbers_dirty = false;
        }
                
        if ($isNew || $sectorsDirty 
            || @$attributesDirty['name'] || @$attributesDirty['username'] || @$attributesDirty['region'])
        {
            Sphinx::reindex();
        }        
        
        return $res;
    }
    
    function render_email_template($template)
    {
        $args = array();
        foreach ($this->attributes as $k => $v)
        {
            $args["{{".$k."}}"] = $v;
            $args["%7B%7B".$k."%7D%7D"] = $v;
        }
   
        return strtr($template, $args);
    }
         
    protected $phone_numbers;
    protected $phone_numbers_dirty = false;

    function query_phone_numbers()
    {
        return OrgPhoneNumber::query()->where('org_guid = ?', $this->guid);
    }
        
    private function set_phone_number($phone_number_str)
    {
        $phone_numbers = OrgPhoneNumber::split_phone_number($phone_number_str, $this->country);

        $this->phone_numbers = array();        
        foreach ($phone_numbers as $phone_number)
        {
            if ($this->guid)
            {   
                $orgPhoneNumber = $this->query_phone_numbers()
                    ->where('phone_number = ?', $phone_number)->get();
            }
            if (!$orgPhoneNumber)
            {
                $orgPhoneNumber = new OrgPhoneNumber();
                $orgPhoneNumber->org_guid = $this->guid;
                $orgPhoneNumber->phone_number = $phone_number;
            }            
            $this->phone_numbers[] = $orgPhoneNumber;
        }        
        $this->phone_numbers_dirty = true;
    }            

    /* 
     * WidgetContainer methods - an Organization is a container for Widgets
     * which are shown as pages on their site.
     */
    function is_page_container()
    {
        return true;
    }        
    
    function get_edit_url()
    {
        return $this->get_url() . "/dashboard";
    }    
    
    function new_child_widget_from_input()
    {        
        $widget_name = get_input('widget_name');
        if (!$widget_name || !Widget::is_valid_name($widget_name))
        {
            throw new ValidationException(__('widget:bad_name'));            
        }
        
        $widget = $this->get_widget_by_name($widget_name);
        
        if ($widget->guid && ((time() - $widget->time_created > 30) || !($widget instanceof Widget_Generic)))
        {
            throw new ValidationException(
                sprintf(__('widget:duplicate_name'),
                    "<a href='{$widget->get_edit_url()}'><strong>".__('clickhere')."</strong></a>"),
                true
            );
        }
        return $widget;
    }
    
    function render_add_child()
    {
        return view("widgets/add", array('org' => $this));
    }
}