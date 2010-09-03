<?php

class Organization extends User
{
    static $subtype_id = T_organization;

    public function is_setup_complete()
    {
        return $this->setup_state >= 5;
    }
    
    function query_news_updates()
    {
        return NewsUpdate::query()->where("container_guid=?", $this->guid)->order_by('time_created desc');
    }    
    
    public function query_files()
    {    
        return UploadedFile::query()->where('container_guid=?',$this->guid);
    }
    
    public function query_reports()
    {
        return Report::query()->where('container_guid=?',$this->guid);
    }

    public function query_report_definitions()
    {
        return ReportDefinition::query()->where('container_guid=?',$this->guid);
    }
    
    function query_partnerships()
    {
        return Partnership::query()->where("container_guid = ? AND approval >= 3", $this->guid);
    }

    public function get_website_score()    
    {
        $score = 0;
    
        if ($this->query_news_updates()->where('time_created > ?', time() - 86400 * 31)->count() > 0)
        {
            $score += 10;
        }
        
        if ($this->query_files()->where("size='small'")->count() >= 2)
        {
            $score += 10;
        }
        
        if (sizeof($this->get_contact_info()) >= 2)
        {   
            $score += 10;
        }
        
        $numWidgets = 0;        
        foreach (array('history','projects','team') as $widgetName)
        {
            $widget = $this->get_widget_by_name($widgetName);
            if ($widget->is_active() && $widget->content)
            {
                $numWidgets++;
            }
        }
             
        if ($numWidgets >= 2)
        {
            $score += 10;
        }
        
        return $score;
    }
    
    public function get_feed_names()
    {
        $feedNames = parent::get_feed_names();

        if ($this->region)
        {
            $feedNames[] = get_feed_name(array("region" => $this->region));
        }

        foreach ($this->get_sectors() as $sector)
        {
            $feedNames[] = get_feed_name(array('sector' => $sector));

            if ($this->region)
            {
                $feedNames[] = get_feed_name(array('region' => $this->region, 'sector' => $sector));
            }
        }

        return $feedNames;

    }
    
    public function get_related_feed_names()
    {
        $feedNames = array();
        $sectors = $this->get_sectors();

        foreach ($sectors as $sector)
        {
            $feedNames[] = get_feed_name(array('sector' => $sector));
        }

        /*
        if ($org->region)
        {
            $feedNames[] = get_feed_name(array('region' => $this->region));
        }
        */

        foreach ($this->query_partnerships()->limit(25)->filter() as $partnership)
        {
            $feedNames[] = get_feed_name(array('user' => $partnerhip->partner_guid));
        }

        return $feedNames;
    }

    public function can_view()
    {
        return $this->approval > 0 || $this->can_edit();
    }
    
    public function get_contact_info()
    {
        $res = array();
                
        $fields = array('mailing_address','street_address','phone_number','email');
        
        foreach ($fields as $field)
        {
            $val = $this->get($field);
            if ($val)
            {
                $res[$field] = $val;
            }
        }
        return $res;
    }

    public function get_available_themes()
    {
        $themes = array('green','brick','craft4','craft1','cotton2','wovengrass','beads','red');
        
        if ($this->username == 'envaya')
        {
            $themes[] = 'sidebar';
        }        
        
        return $themes;
    }
    
    public function get_country_text()
    {
        return __("country:{$this->country}");
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

    static function get_sector_options()
    {
        $sectors = array(
            1 => __('sector:agriculture'),
            2 => __('sector:communications'),
            3 => __('sector:conflict_res'),
            4 => __('sector:cooperative'),
            5 => __('sector:culture'),
            6 => __('sector:education'),
            7 => __('sector:environment'),
            8 => __('sector:health'),
            9 => __('sector:hiv_aids'),
            13 => __('sector:human_rights'),
            14 => __('sector:labor_rights'),
            15 => __('sector:microenterprise'),
            16 => __('sector:natural_resources'),
            17 => __('sector:prof_training'),
            18 => __('sector:rural_dev'),
            19 => __('sector:sci_tech'),
            20 => __('sector:substance_abuse'),
            21 => __('sector:tourism'),
            22 => __('sector:trade'),
            23 => __('sector:women'),
        );

        asort($sectors);

        $sectors[SECTOR_OTHER] = __('sector:other');

        return $sectors;
    }

    public function get_sectors()
    {
        if (!isset($this->sectors))
        {
            $sectorRows = get_data("select * from org_sectors where container_guid = ?", array($this->guid));
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
        if ($this->sectors_dirty)
        {
            delete_data("delete from org_sectors where container_guid = ?", array($this->guid));
            foreach ($this->sectors as $sector)
            {
                insert_data("insert into org_sectors (container_guid, sector_id) VALUES (?,?)", array($this->guid, $sector));
            }
            $this->sectors_dirty = false;
        }

        return parent::save();
    }

    public function get_widget_by_name($name)
    {
        $widget = Widget::query()->where('container_guid=?', $this->guid)->where('widget_name=?',$name)->show_disabled(true)->get();
        
        if (!$widget)
        {
            $widget = new Widget();
            $widget->container_guid = $this->guid;
            $widget->widget_name = $name;
        }
        return $widget;
    }

    private function get_saved_widgets()
    {
        return Widget::query()->where('container_guid=?',$this->guid)->filter();
    }
    
    public function get_available_widgets()
    {        
        $savedWidgetsMap = array();
        $availableWidgets = array();
        
        foreach ($this->get_saved_widgets() as $widget)
        {
            $savedWidgetsMap[$widget->widget_name] = $widget;
            $availableWidgets[] = $widget;
        }        

        foreach (Widget::get_default_names() as $name)
        {
            if (!isset($savedWidgetsMap[$name]))
            {
                $widget = new Widget();
                $widget->container_guid = $this->guid;
                $widget->widget_name = $name;
                $availableWidgets[] = $widget;
            }            
        }        
        usort($availableWidgets, array('Widget', 'sort'));
        return $availableWidgets;
    }
    
    static function query_search($name, $sector, $region)
    {
        $query = static::query();
        
        if ($name)
        {
            $query->where("(INSTR(u.username, ?) > 0 OR INSTR(u.name, ?) > 0)", $name, $name);
        }

        if ($sector)
        {
            $query->join("INNER JOIN org_sectors s ON s.container_guid = e.guid");
            $query->where("s.sector_id=?", $sector);
        }

        if ($region)
        {
            $query->where("region=?", $region);
        }
        $query->order_by('u.name');
        
        return $query;
    }

    static function list_search($name, $sector, $region, $limit = 10)
    {
        $offset = (int) get_input('offset');

        $query = static::query_search($name, $sector, $region);
        
        $query->limit($limit, $offset);
        
        $count = $query->count();
        $entities = $query->filter();

        return view_entity_list($entities, $count, $offset, $limit);
    }

    static function query_by_area($latLongArr, $sector)
    {
        $query = static::query();
        $query->where("latitude >= ?", $latLongArr[0]);
        $query->where("latitude <= ?", $latLongArr[2]);
        $query->where("longitude >= ?", $latLongArr[1]);
        $query->where("longitude <= ?", $latLongArr[3]);

        if ($sector)
        {
            $query->join("INNER JOIN org_sectors s ON s.container_guid = e.guid");
            $query->where("s.sector_id=?", $sector);
        }

        return $query;
    }
    
    function get_partnership($partnerOrg)
    {
        $partnership = Partnership::query()->where('container_guid=?',$this->guid)->where('partner_guid=?',$partnerOrg->guid)->get();

        if (!$partnership)
        {
            $partnership = new Partnership();
            $partnership->container_guid = $this->guid;
            $partnership->partner_guid = $partnerOrg->guid;
        }
        return $partnership;
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
}