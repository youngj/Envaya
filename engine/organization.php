<?php

class Organization extends ElggUser
{
    protected function initialise_attributes()
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = T_organization;
    }

    static $subtype_id = T_organization;

    public function queryFiles()
    {    
        return ElggFile::query()->where('container_guid=?',$this->guid);
    }
    
    public function getFeedNames()
    {
        $feedNames = parent::getFeedNames();

        if ($this->region)
        {
            $feedNames[] = get_feed_name(array("region" => $this->region));
        }

        foreach ($this->getSectors() as $sector)
        {
            $feedNames[] = get_feed_name(array('sector' => $sector));

            if ($this->region)
            {
                $feedNames[] = get_feed_name(array('region' => $this->region, 'sector' => $sector));
            }
        }

        return $feedNames;

    }
    
    public function getRelatedFeedNames()
    {
        $feedNames = array();
        $sectors = $this->getSectors();

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

        foreach ($this->queryPartnerships()->limit(25)->filter() as $partnership)
        {
            $feedNames[] = get_feed_name(array('user' => $partnerhip->partner_guid));
        }

        return $feedNames;
    }

    public function canView()
    {
        return $this->approval > 0 || $this->canEdit();
    }

    public function canCommunicateWith()
    {
        return $this->canView() && Session::isloggedin() && Session::get_loggedin_userid() != $this->guid;
    }
    
    public function getContactInfo()
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

    public function showCantViewMessage()
    {
        if ($this->approval == 0)
        {
            system_message(__('approval:waiting'));
        }
        else if ($this->approval < 0)
        {
            system_message(__('approval:rejected'));
        }
    }

    public function getAvailableThemes()
    {
        $themes = get_themes();
        if ($this->username == 'envaya')
        {
            $themes[] = 'sidebar';
        }        
        return $themes;
    }
    
    public function generateEmailCode()
    {
        $code = '';
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        for ($p = 0; $p < 8; $p++)
        {
            $code .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        $this->email_code = $code;
        $this->save();
    }

    public function getPostEmail()
    {
        if (!$this->email_code)
        {
            $this->generateEmailCode();
        }
        global $CONFIG;
        $postEmailParts = explode('@', $CONFIG->post_email, 2);
        return "{$postEmailParts[0]}+{$this->email_code}@{$postEmailParts[1]}";
    }

    public function getCountryText()
    {
        return __("country:{$this->country}");
    }

    public function getLocationText($includeRegion = true)
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
        $res .= $this->getCountryText();

        return $res;
    }

    protected $sectors;
    protected $sectors_dirty = false;

    static function getSectorOptions()
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

    public function getSectors()
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

    public function setSectors($arr)
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

    public function getWidgetByName($name)
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

    private function getSavedWidgets()
    {
        return Widget::query()->where('container_guid=?',$this->guid)->filter();
    }
    
    public function getAvailableWidgets()
    {        
        $savedWidgetsMap = array();
        $availableWidgets = array();
        
        foreach ($this->getSavedWidgets() as $widget)
        {
            $savedWidgetsMap[$widget->widget_name] = $widget;
            $availableWidgets[] = $widget;
        }        

        foreach (Widget::getDefaultNames() as $name)
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
    
    static function querySearch($name, $sector, $region)
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

    static function listSearch($name, $sector, $region, $limit = 10)
    {
        $offset = (int) get_input('offset');

        $query = static::querySearch($name, $sector, $region);
        
        $query->limit($limit, $offset);
        
        $count = $query->count();
        $entities = $query->filter();

        return view_entity_list($entities, $count, $offset, $limit);
    }

    static function queryByArea($latLongArr, $sector)
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

    function queryPartnerships()
    {
        return Partnership::query()->where("container_guid = ? AND approval >= 3", $this->guid);
    }
    
    function getPartnership($partnerOrg)
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