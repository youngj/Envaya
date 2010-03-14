<?php 

require_once(dirname(__FILE__)."/users.php");

define('SECTOR_OTHER', 99);

// Class source
class Organization extends ElggUser {

    protected function initialise_attributes() 
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = T_organization;

        //Notes:
        // this->isVerifyingOrg
        // verifiedBy... relationship
        // when admin performs verification, he/she chooses from a dropdown of the Orgs he/she is an admin of.  Organization is then verified by this chosen super org
        // entity_relationships
        //addRelationship(...
    }

    static $subtype_id = T_organization;

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
        
    public function getIconFile($size)
    {
        $filehandler = new ElggFile();
        $filehandler->owner_guid = $this->guid;
        $filehandler->setFilename("icon$size.jpg");
        return $filehandler;
    }   

    public function getPostEmail()
    {
        if (!$this->email_code)
        {
            $this->generateEmailCode();
        }
        return "post+{$this->email_code}@envaya.org";
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
            $res .= "{$this->region}, ";
        }
        $res .= elgg_echo("country:{$this->country}");
        
        return $res;
    }
       
    protected $sectors;   
    protected $sectors_dirty = false;
       
    static function getSectorOptions()
    {
        return array(
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
            SECTOR_OTHER => elgg_echo('sector:other'),
        );
    }
          
    static function filterBySector($sector)
    {
        
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
        $allNames = array('home', 'map', 'history', 'team', 'programs', 'achievements', 'challenges', 'contact');
    
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
}

class Translation extends ElggObject
{
    static $subtype_id = T_translation;
    static $table_name = 'translations';
    static $table_attributes = array(
        'hash' => '',
        'property' => '',
        'lang' => '',
        'value' => ''
    );
    
    public function save()
    {
        $this->hash = $this->calculateHash();
        return parent::save();
    }
    
    public function getOriginalText()
    {
        $obj = $this->getContainerEntity();
        $property = $this->property;
        return trim($obj->$property);
    }
    
    public function calculateHash()
    {
        return $this->getRootContainerEntity()->language . ":" . sha1($this->getOriginalText());        
    }    
    
    public function isStale()
    {
        return $this->calculateHash() != $this->hash;
    }
}

class Widget extends ElggObject
{
    static $subtype_id = T_widget;
    static $table_name = 'widgets';
    static $table_attributes = array(
        'widget_name' => 0,
        'content' => '',
        'data_types' => 0,
    );        
    
    function renderView()
    {
        $res = elgg_view("widgets/{$this->widget_name}_view", array('widget' => $this));
        if ($res)
        {
            return $res;
        }    
        return elgg_view("widgets/generic_view", array('widget' => $this));    
    }
    
    function renderEdit()
    {
        $res = elgg_view("widgets/{$this->widget_name}_edit", array('widget' => $this));
        if ($res)
        {
            return $res;
        }    
        return elgg_view("widgets/generic_edit", array('widget' => $this));
    } 
    
    function getURL()
    {
        $org = $this->getContainerEntity();
        return "{$org->getUrl()}/{$this->widget_name}";
    }
    
    function saveInput()
    {
        $fn = "save_widget_{$this->widget_name}";
        if (!is_callable($fn))
        {
            $fn = "save_widget";
        }
        $fn($this);
    }    

    public function getImageFile($size = '')
    {
        $filehandler = new ElggFile();
        $filehandler->owner_guid = $this->container_guid;
        $filehandler->setFilename("widget/{$this->guid}$size.jpg");
        return $filehandler;       
    }
    
    public function hasImage()
    {
        return ($this->data_types & DataType::Image) != 0;
    }   
    
    public function getImageURL($size = 'large')
    {
        return "{$this->getUrl()}/image/{$size}?{$this->time_updated}";
    }

    public function setImage($imageData)
    {
        if (!$imageData)
        {
            $this->data_types &= ~DataType::Image;     
        }
        else
        {
            $this->data_types |= DataType::Image; 

            $prefix = "widget/{$this->guid}";

            $file = new ElggFile();
            $file->owner_guid = $this->container_guid;
            $file->container_guid = $this->guid;

            $file->setFilename("{$prefix}.jpg");
            $file->open("write");
            $file->write($imageData);
            $file->close();

            $originalFileName = $file->getFilenameOnFilestore();

            $thumbsmall = get_resized_image_from_existing_file($originalFileName,100,150, false);
            if ($thumbsmall) 
            {
                $file->setFilename("{$prefix}small.jpg");
                $file->open("write");
                $file->write($thumbsmall);
                $file->close();
            }            

            $thumbmed = get_resized_image_from_existing_file($originalFileName,200,300, false);
            if ($thumbmed) 
            {
                $file->setFilename("{$prefix}medium.jpg");
                $file->open("write");
                $file->write($thumbmed);
                $file->close();
            }
            
            $thumblarge = get_resized_image_from_existing_file($originalFileName,450,450, false);
            if ($thumblarge) 
            {
                $file->setFilename("{$prefix}large.jpg");
                $file->open("write");
                $file->write($thumblarge);
                $file->close();
            }
            
        }   
        $this->save();
    } 
    
    public function isActive()
    {
        return $this->guid && $this->isEnabled();
    }
}

function save_widget($widget)
{
    $widget->content = get_input('content');
    $widget->image_position = get_input('image_position');
    $widget->save();
    
    if (isset($_FILES['image']) && $_FILES['image']['size'])
    {            
        if (substr_count($_FILES['image']['type'],'image/'))
        {    
            $widget->setImage(get_uploaded_file('image'));        
        }
        else
        {
            register_error(elgg_echo('upload:invalid_image'));
        }
    }    
    else if (get_input('deleteimage'))
    {
        $widget->setImage(null);
    }
}

function save_widget_home($widget)
{
    $widget->content = get_input('content');
    $org = $widget->getContainerEntity();    
    $org->setSectors(get_input_array('sector'));
    $org->sector_other = get_input('sector_other');
    $org->save();
    $widget->save();
}

function save_widget_map($widget)
{
    $org = $widget->getContainerEntity();
    $org->latitude = get_input('org_lat');
    $org->longitude = get_input('org_lng');    
    $org->save();
    
    $widget->zoom = get_input('map_zoom');    
    $widget->save();
}

function save_widget_contact($widget)
{
    $org = $widget->getContainerEntity();
    $widget->public_email = get_input('public_email');
    $org->phone_number = get_input('phone_number');    
    $org->contact_name = get_input('contact_name');    
    $org->contact_title = get_input('contact_title');    
    $org->save();
    $widget->save();
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
        $filehandler = new ElggFile();
        $filehandler->owner_guid = $this->container_guid;
        $filehandler->setFilename("blog/{$this->guid}$size.jpg");
        return $filehandler;       
    }
    
    public function getImageURL($size = '')
    {
        return "{$this->getURL()}/image/$size?{$this->time_updated}";
    }    
    
    public function hasImage()
    {
        return ($this->data_types & DataType::Image) != 0;
    }   

    public function setImage($imageData)
    {
        if (!$imageData)
        {
            $this->data_types &= ~DataType::Image;     
        }
        else
        {        
            $this->data_types |= DataType::Image; 

            $prefix = "blog/".$this->guid;

            $filehandler = new ElggFile();

            $filehandler->owner_guid = $this->container_guid;
            $filehandler->container_guid = $this->guid;

            $filehandler->setFilename($prefix . ".jpg");
            $filehandler->open("write");
            $filehandler->write($imageData);
            $filehandler->close();

            $thumbsmall = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),100,100, false);
            $thumblarge = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),450,450, false);

            if ($thumbsmall) 
            {
                $thumb = new ElggFile();
                $thumb->owner_guid = $blog->container_guid;
                $thumb->container_guid = $blog->guid;
                $thumb->setMimeType('image/jpeg');

                $thumb->setFilename($prefix."small.jpg");
                $thumb->open("write");
                $thumb->write($thumbsmall);
                $thumb->close();

                $thumb->setFilename($prefix."large.jpg");
                $thumb->open("write");
                $thumb->write($thumblarge);
                $thumb->close();
            }        
        }   
        $this->save();
    }
}

class TranslateMode
{
    const None = 1;
    const ManualOnly = 2;
    const All = 3;    
}

function view_translated($obj, $field)
{        
    $text = trim($obj->$field);
    if (!$text)
    {
        return '';
    }   

    $org = $obj->getRootContainerEntity();
    if (!($org instanceof Organization))
    {
        return '';
    }
    
    $origLang = $org->language;
    $viewLang = get_language();

    if ($origLang != $viewLang)
    {
        global $CONFIG;
        
        if (!isset($CONFIG->translations_available))
        {
            $CONFIG->translations_available = array('origlang' => $origLang);
        }

        $translateMode = get_translate_mode();
        $translation = lookup_translation($obj, $field, $origLang, $viewLang, $translateMode);            
        
        if ($translation && $translation->owner_guid)
        {
            $CONFIG->translations_available[TranslateMode::ManualOnly] = true;            
            
            if ($translation->isStale())
            {
                $CONFIG->translations_available['stale'] = true;
            }
            
            $viewTranslation = ($translateMode > TranslateMode::None);
        }
        else
        {
            $CONFIG->translations_available[TranslateMode::All] = true;
            $viewTranslation = ($translateMode == TranslateMode::All);
        }

        return elgg_view("translation/wrapper", array(
            'translation' => $viewTranslation ? $translation : null, 
            'entity' => $obj, 
            'property' => $field, 
        ));
    }   

    return elgg_view("output/longtext",array('value' => $text));        
}


function lookup_translation($obj, $prop, $origLang, $viewLang, $translateMode = TranslateMode::ManualOnly)
{
    $where = array();
    $args = array();

    $where[] = "subtype=?";
    $args[] = T_translation;

    $where[] = "property=?";
    $args[] = $prop;

    $where[] = "lang=?";
    $args[] = $viewLang;

    $where[] = "container_guid=?";
    $args[] = $obj->guid;

    $entities = get_entities_by_condition('translations', $where, $args, '', 1);          
    
    $doAutoTranslate = ($translateMode == TranslateMode::All);
    
    if (!empty($entities)) 
    {        
        $trans = $entities[0];
        
        if ($doAutoTranslate && $trans->isStale())
        {
            $text = get_auto_translation($obj->$prop, $origLang, $viewLang);
            if ($text != null)
            {
                if (!$trans->owner_guid) // previous version was from google
                {            
                    $trans->value = $text;
                    $trans->save();
                }
                else // previous version was from human
                {
                    // TODO : cache this
                    $fakeTrans = new Translation();    
                    $fakeTrans->owner_guid = 0;
                    $fakeTrans->container_guid = $obj->guid;
                    $fakeTrans->property = $prop;
                    $fakeTrans->lang = $viewLang;
                    $fakeTrans->value = $text;                               
                    return $fakeTrans;
                }        
            }    
        }    
        
        return $trans;
    }
    else if ($doAutoTranslate)
    {   
        $text = get_auto_translation($obj->$prop, $origLang, $viewLang);
        
        if ($text != null)
        {
            $trans = new Translation();    
            $trans->owner_guid = 0;
            $trans->container_guid = $obj->guid;
            $trans->property = $prop;
            $trans->lang = $viewLang;
            $trans->value = $text;            
            $trans->save();
            return $trans;
        }    
        return null;
    }
    return null;
}

function get_auto_translation($text, $origLang, $viewLang)
{
    if ($origLang == $viewLang)
    {
        return null;
    }    

    $text = trim($text);
    if (!$text)
    {
        return null;
    }
           
    $ch = curl_init(); 
    
    $text = str_replace("\r","", $text);
    $text = str_replace("\n", ",;", $text);
    
    $url = "ajax.googleapis.com/ajax/services/language/translate?v=1.0&langpair=$origLang%7C$viewLang&q=".urlencode($text);
    
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_REFERER, "www.envaya.org");     
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    
    $json = curl_exec($ch); 
         
    curl_close($ch);     
    
    $res = json_decode($json);
                
    $translated = $res->responseData->translatedText;
    if (!$translated)
    {
        return null;
    }
            
    $text = html_entity_decode($translated, ENT_QUOTES);
    
    return str_replace(",;", "\n", $text);   
}

function envaya_init() {

    global $CONFIG;

    org_fields_setup();
    
    register_plugin_hook('geocode', 'location', 'googlegeocoder_geocode');
    
    register_entity_type('user', 'organization');
    register_entity_type('object', 'blog');
    register_entity_type('object', 'translation');
    
    register_entity_url_handler('org_url','user','organization');
    register_entity_url_handler('blogpost_url','object','blog');

    extend_view('css','org/css');

    register_plugin_hook('entity:icon:url', 'user', 'org_icon_hook');
    
    include_once("{$CONFIG->path}org/start.php");    
}

/**
 * Mobworking.net geocoder
 * 
 * @author Marcus Povey <marcus@dushka.co.uk>
 * @copyright Marcus Povey 2008-2009
 */
    
/** 
 * Google geocoder.
 *
 * Listen for an Elgg Geocode request and use google maps to geocode it.
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

/**
 * Populates the ->getUrl() method for org objects
 *
 * @param ElggEntity $entity File entity
 * @return string File URL
 */
function org_url($entity) {

    global $CONFIG;
    return $CONFIG->url . "{$entity->username}";
}

function forward_to_referrer()
{
    forward($_SERVER['HTTP_REFERER']);    
}

function blogpost_url($blogpost) {

    global $CONFIG;
    
    $org = $blogpost->getContainerEntity();
    
    if ($org)
    {    
        return $org->getUrl() . "/post/" . $blogpost->getGUID();
    }
}

function org_icon_hook($hook, $entity_type, $returnvalue, $params)
{
    global $CONFIG;

    $entity = $params['entity'];        

    if ($entity instanceof Organization)
    {
        $size = $params['size'];

        $icontime = ($entity->icontime) ? $entity->icontime : "default";
        $file = $entity->getIconFile();
        if ($file->exists())
        {
            return "{$CONFIG->url}{$entity->username}/icon/$size/$icontime.jpg";
        }
        else if ($entity->latitude || $entity->longitude)
        {
            return get_static_map_url($entity->latitude, $entity->longitude, 6, 100, 100);
        }            
        else
        {
            return "{$CONFIG->url}_graphics/default{$size}.gif";
        }
    }
}


function org_fields_setup()
{
    global $CONFIG;

    $CONFIG->org_fields = array(
        'name' => 'text',        
        'username' => 'text',        
        'password' => 'password',
        'password2' => 'password',
        'email' => 'email',    
        'language' => 'language',
    );

    $CONFIG->org_profile_fields = array(
        'description' => 'longtext',
        'phone' => 'text',
        'website' => 'url',        
        'location' => 'text',
    );
}

function preserve_input($name, $value)
{    
    $prevInput = $_SESSION['input'];
    if ($prevInput)
    {
        if (isset($prevInput[$name]))
        {
            $val = $prevInput[$name];
            unset($_SESSION['input'][$name]);
            return $val;
        }    
    }
    return $value;
}

function regions_in_country($country)
{
    if ($country == 'tz'  || true)
    {
        return array(
            'Arusha',
            'Dar es Salaam',
            'Dodoma',
            'Iringa',
            'Kagera',
            'Kigoma',
            'Kilimanjaro',
            'Lindi',
            'Manyara',
            'Mara',
            'Mbeya',
            'Morogoro',
            'Mtwara',
            'Mwanza',
            'Pemba North',
            'Pemba South',
            'Pwani',
            'Rukwa',
            'Ruvuma',
            'Shinyanga',
            'Singida',
            'Tabora',
            'Tanga',
            'Zanzibar Central/South',
            'Zanzibar North',
            'Zanzibar West',
        );
    }
    else
    {
        return array();
    }
}

function get_static_map_url($lat, $long, $zoom, $width, $height)
{
    global $CONFIG;
    $apiKey = $CONFIG->google_api_key;
    return "http://maps.google.com/maps/api/staticmap?center=$lat,$long&zoom=$zoom&size={$width}x$height&maptype=roadmap&markers=$lat,$long&sensor=false&key=$apiKey";
}

function get_translate_mode()
{
    return ((int)get_input("trans")) ?: TranslateMode::ManualOnly;
}

function get_original_language()
{
    global $CONFIG;
    if (isset($CONFIG->translations_available))
    {
        return $CONFIG->translations_available['origlang'];
    }
    
    return '';
}

function page_has_stale_translation()
{
    global $CONFIG;
    return (isset($CONFIG->translations_available) && isset($CONFIG->translations_available['stale']));    
}

function page_is_translatable($mode=null)
{
    global $CONFIG;
    
    if (isset($CONFIG->translations_available))
    {
        if ($mode == null || isset($CONFIG->translations_available[$mode]))
        {
            return true;
        }
    }
    return false;
}

register_elgg_event_handler('init','system','envaya_init');
