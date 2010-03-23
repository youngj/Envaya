<?php 

require_once(dirname(__FILE__)."/users.php");

define('SECTOR_OTHER', 99);

class Organization extends ElggUser 
{
    protected function initialise_attributes() 
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = T_organization;
    }

    static $subtype_id = T_organization;

    public function canView()
    {
        return $this->approval > 0 || $this->canEdit();
    }
    
    public function showCantViewMessage()    
    {
        if ($this->approval == 0)
        {
            system_message(elgg_echo('org:waitingapproval'));
            
            
        }
        else if ($this->approval < 0)
        {
            system_message(elgg_echo('org:rejected'));
        }    
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
            
    public function getURL()
    {
        global $CONFIG;
        return $CONFIG->url . "{$this->username}";
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
    
    public function getLocationText($includeRegion = true)
    {
        $res = '';
        
        if ($this->city)
        {
            $res .= "{$this->city}, ";
        }
        if ($this->region && $includeRegion)
        {
            $res .= elgg_echo($this->region). ", ";            
        }
        $res .= elgg_echo("country:{$this->country}");
        
        return $res;
    }
       
    protected $sectors;   
    protected $sectors_dirty = false;
       
    static function getSectorOptions()
    {
        $sectors = array(
            1 => elgg_echo('sector:agriculture'),
            2 => elgg_echo('sector:communications'),
            3 => elgg_echo('sector:conflict_res'),
            4 => elgg_echo('sector:cooperative'),
            5 => elgg_echo('sector:culture'),
            6 => elgg_echo('sector:education'),
            7 => elgg_echo('sector:environment'),
            8 => elgg_echo('sector:health'),
            9 => elgg_echo('sector:hiv_aids'),
            13 => elgg_echo('sector:human_rights'),
            14 => elgg_echo('sector:labor_rights'),
            15 => elgg_echo('sector:microenterprise'),
            16 => elgg_echo('sector:natural_resources'),
            17 => elgg_echo('sector:prof_training'),
            18 => elgg_echo('sector:rural_dev'),
            19 => elgg_echo('sector:sci_tech'),
            20 => elgg_echo('sector:substance_abuse'),
            21 => elgg_echo('sector:tourism'),
            22 => elgg_echo('sector:trade'),
            23 => elgg_echo('sector:women'),
        );
        
        asort($sectors);
        
        $sectors[SECTOR_OTHER] = elgg_echo('sector:other');
        
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
        $where = array();
        $args = array();
        
        $where[] = "container_guid=?";
        $args[] = $this->guid;
        
        $where[] = "widget_name=?";
        $args[] = $name;
    
        $showHidden = access_get_show_hidden_status();
        access_show_hidden_entities(true);
    
        $widget = Widget::getByCondition($where, $args);
        
        $showHidden = access_show_hidden_entities($showHidden);
        
        if (!$widget)
        {
            $widget = new Widget();
            $widget->container_guid = $this->guid;
            $widget->widget_name = $name;
        }
        return $widget;
    }
    
    public function getActiveWidgets()
    {
        $where = array();
        $args = array();
        
        $where[] = "container_guid=?";
        $args[] = $this->guid;        
        
        return Widget::filterByCondition($where, $args);
    }
    
    public function getAvailableWidgets()
    {
        $allNames = array('home', 'news', 'map', 'history', 'team', 'programs', 'contact');
    
        $activeWidgets = $this->getActiveWidgets();
        
        $activeWidgetsMap = array();
        foreach ($activeWidgets as $widget)
        {
            $activeWidgetsMap[$widget->widget_name] = $widget;
        }
        
        $availableWidgets = array();
        foreach ($allNames as $name)
        {
            if (isset($activeWidgetsMap[$name]))
            {
                $widget = $activeWidgetsMap[$name];
            }
            else
            {
                $widget = new Widget();
                $widget->container_guid = $this->guid;
                $widget->widget_name = $name;                
            }
            $availableWidgets[] = $widget;
        }
        return $availableWidgets;
    }

    static function search($name, $sector, $region, $limit = 10, $offset = 0, $count = false)
    {
        $where = array();
        $args = array();
        if ($name)
        {
            $where[] = "(INSTR(u.username, ?) > 0 OR INSTR(u.name, ?) > 0)";
            $args[] = $name;
            $args[] = $name;
        }    

        $join = '';
        if ($sector)
        {
            $join = "INNER JOIN org_sectors s ON s.container_guid = e.guid";
            $where[] = "s.sector_id=?";
            $args[] = $sector;
        }
        
        if ($region)
        {
            $where[] = "region=?";
            $args[] = $region;
        }

        return static::filterByCondition($where, $args, '', $limit, $offset, $count, $join);
    }

    static function listSearch($name, $sector, $region, $limit = 10, $pagination = true) 
    {        
        $offset = (int) get_input('offset');

        $count = static::search($name, $sector, $region, $limit, $offset, true);
        $entities = static::search($name, $sector, $region, $limit, $offset);

        return elgg_view_entity_list($entities, $count, $offset, $limit, false, false, $pagination);
    }        
    
    static function filterByArea($latLongArr, $sector, $limit = 10, $offset = 0, $count = false)
    {
        $where = array();
        $args = array();

        $where[] = "latitude >= ?";
        $args[] = $latLongArr[0];

        $where[] = "latitude <= ?";
        $args[] = $latLongArr[2];

        $where[] = "longitude >= ?";
        $args[] = $latLongArr[1];

        $where[] = "longitude <= ?";
        $args[] = $latLongArr[3];

        $join = '';
        if ($sector)
        {
            $join = "INNER JOIN org_sectors s ON s.container_guid = e.guid";
            $where[] = "s.sector_id=?";
            $args[] = $sector;
        }

        return static::filterByCondition($where, $args, '', $limit, $offset, $count, $join);
    }    
}

class DataType
{
    const Image = 2;
}

class NewsUpdate extends ElggObject
{
    static $subtype_id = T_blog;
    static $table_name = 'news_updates';
    static $table_attributes = array(
        'content' => '',
        'data_types' => 0,
    );        
    
    public function getImageFile($size = '')
    {
        $file = new ElggFile();
        $file->owner_guid = $this->container_guid;
        $file->setFilename("news/{$this->guid}$size.jpg");
        return $file;       
    }
    
    public function jsProperties()
    {
        return array(
            'guid' => $this->guid,
            'container_guid' => $this->container_guid,
            'dateText' => $this->getDateText(),
            'imageURL' => $this->getImageURL('small'),
            'snippetHTML' => $this->getSnippetHTML()
        );
    }    

    public function getURL()
    {
        $org = $this->getContainerEntity();
        if ($org)
        {    
            return $org->getUrl() . "/post/" . $this->getGUID();
        }
        return '';
    }        
    
    public function getImageURL($size = '')
    {
        return $this->hasImage() ? ($this->getImageFile($size)->getURL()."?{$this->time_updated}") : "";
    }    
    
    public function hasImage()
    {
        return ($this->data_types & DataType::Image) != 0;
    }   
    
    public function getSnippetHTML($maxLength = 100)
    {
        $content = $this->content;
        if ($content)
        {
            // todo: multi-byte support
            if (strlen($content) > $maxLength)
            {
                $content = substr($content, 0, $maxLength) . "...";
            }                
            
            return elgg_view('output/text', array('value' => $content));
        }
        return '';
    }

    public function getDateText()
    {
        return friendly_time($this->time_created); 
    }
    
    public function setImage($imageFilePath)
    {
        if (!$imageFilePath)
        {
            $this->data_types &= ~DataType::Image;     
        }
        else
        {        
            if ($this->getImageFile('small')->uploadFile(resize_image_file($imageFilePath,100,100))
               && $this->getImageFile('large')->uploadFile(resize_image_file($imageFilePath,450,450)))
            {
                $this->data_types |= DataType::Image;  
            }
            else            
            {
                throw new DataFormatException("error saving image");
            }            
        }   
        $this->save();
    }
    
    public static function all($limit = 10, $offset = 0)
    {
        return static::filterByCondition(array(), array(), 'time_created desc', $limit, $offset);
    }
    
    public static function filterByOrganizations($orgs, $limit = 10, $offset = 0)
    {
        if (empty($orgs))
        {
            return array();
        }
        else
        {
            $where = array();
            $args = array();
            $in = array();
            
            foreach ($orgs as $org)
            {
                $in[] = "?";
                $args[] = $org->guid;
            }
            
            $where[] = "container_guid IN (".implode(",", $in).")";

            return static::filterByCondition($where, $args, 'time_created desc', $limit, $offset);
        }    
    }
}

/**
 * Mobworking.net geocoder
 * 
 * @author Marcus Povey <marcus@dushka.co.uk>
 * @copyright Marcus Povey 2008-2009
 */  
function googlegeocoder_geocode($hook, $entity_type, $returnvalue, $params)
{ 
    if (isset($params['location']))
    {
        global $CONFIG;
        $google_api = $CONFIG->google_api_key;

        // Desired address
        $address = "http://maps.google.com/maps/geo?q=".urlencode($params['location'])."&output=json&key=" . $google_api;

        // Retrieve the URL contents
        $result = file_get_contents($address);
        $obj = json_decode($result);

        $obj = $obj->Placemark[0]->Point->coordinates;

        if ($obj)
        {           
            return array('lat' => $obj[1], 'long' => $obj[0]);
        }
    }
}

function org_title($org, $subtitle)
{
    return elgg_view('page_elements/title', array(
        'title' => $org->name, 
        'subtitle' => $subtitle, 
        'icon' => $org->getIcon('medium'),
        'link' => $org->getURL()                   
    ));
}

function regions_in_country($country)
{
    if ($country == 'tz'  || true)
    {
        $ids = array(
            'tz:arusha',
            'tz:dar',
            'tz:dodoma',
            'tz:iringa',
            'tz:kagera',
            'tz:kigoma',
            'tz:kilimanjaro',
            'tz:lindi',
            'tz:manyara',
            'tz:mara',
            'tz:mbeya',
            'tz:morogoro',
            'tz:mtwara',
            'tz:mwanza',
            'tz:pemba_n',
            'tz:pemba_s',
            'tz:pwani',
            'tz:rukwa',
            'tz:ruvuma',
            'tz:shinyanga',
            'tz:singida',
            'tz:tabora',
            'tz:tanga',
            'tz:zanzibar_cs',
            'tz:zanzibar_n',
            'tz:zanzibar_w',
        );        
    }
    else
    {
        $ids = array();
    }
    
    $res = array();
    foreach ($ids as $id)
    {
        $res[$id] = elgg_echo($id);
    }    
    asort($res);
    return $res;
}

function get_static_map_url($lat, $long, $zoom, $width, $height)
{
    global $CONFIG;
    $apiKey = $CONFIG->google_api_key;
    return "http://maps.google.com/maps/api/staticmap?center=$lat,$long&zoom=$zoom&size={$width}x$height&maptype=roadmap&markers=$lat,$long&sensor=false&key=$apiKey";
}

function envaya_init() 
{
    global $CONFIG;
    
    register_plugin_hook('geocode', 'location', 'googlegeocoder_geocode');
    
    register_entity_type('user', 'organization');
    register_entity_type('object', 'blog');
    register_entity_type('object', 'translation');
    
    extend_view('css','org/css');

    include_once("{$CONFIG->path}org/start.php");    
}

register_elgg_event_handler('init','system','envaya_init');
