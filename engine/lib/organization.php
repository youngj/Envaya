<?php 

require_once(dirname(__FILE__)."/users.php");

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

    public function __construct($guid = null) 
    {
        parent::__construct($guid);
    }

    public function set($name, $value)
    {
        if ($name == 'name' || $name == 'username')
        {
            $this->setMetaData($name, $value);
        }
        
        return parent::set($name, $value);
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

    function getBlogDates()
    {
        $sql = "SELECT guid, time_created from entities WHERE type='object' AND subtype=? AND container_guid=? ORDER BY guid ASC";
        return get_data($sql, array(T_blog, $this->guid));               
    }    
}

class Translation extends ElggObject
{
    static $subtype_id = T_translation;

    protected function initialise_attributes() 
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = T_translation;
    }    
    
    public function __construct($guid = null) 
    {
        parent::__construct($guid);
    }      
}

class NewsUpdate extends ElggObject
{
    static $subtype_id = T_blog;

    protected function initialise_attributes() 
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = T_blog;
    }
    
    public function __construct($guid = null) 
    {
        parent::__construct($guid);
        $this->access_id = ACCESS_PUBLIC;
    }
    
    public function getImageFile($size = '')
    {
        $filehandler = new ElggFile();
        $filehandler->owner_guid = $this->container_guid;
        $filehandler->setFilename("blog/{$this->guid}$size.jpg");
        return $filehandler;       
    }
    
    public function setImage($imageData)
    {
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
}

function view_translated($obj, $field)
{        
    $md = get_metadata_byname($obj->guid, $field);        
           
    if (!$md || is_array($md)) 
    {
        return '' ;
    }

    $text = trim($md->value);
    
    if (!$text)
    {
        return '';
    }
    
    $org = $obj->getRootContainerEntity();
    if (!($org instanceof Organization))
    {
        return '';
    }

    $differentLanguage = ($org->language != get_language());    

    if ($differentLanguage)
    {
        $translate = true || get_input("translate");
        
        if ($translate)
        {
            $translation = lookup_translation($text, $org->language);            
            
            return elgg_view("translation/wrapper", array('translation' => $translation, 'metadata' => $md));
        }
        else
        {
            return elgg_view("output/longtext",array('value' => $text));
        }
    }   

    return elgg_view("output/longtext",array('value' => $text));        
}


function get_translation_key($text, $src, $dest)
{
    return $src . ":" . $dest . ":" . sha1(trim($text));
}

function lookup_translation($text, $text_language)
{
    $text = trim($text);
    if (!$text)
    {
        return null;
    }
    
    $disp_language = get_language();
    if ($text_language == $disp_language)
    {
        return null;
    }    
    
    $key = get_translation_key($text, $text_language, $disp_language);
        
    $translations = get_entities_from_metadata('key', $key, 'object', 'translation'); 
    if (!empty($translations))
    {        
        return $translations[0];
    }
    
    $ch = curl_init(); 
    
    $text = str_replace("\r","", $text);
    $text = str_replace("\n", ",;", $text);
    
    $url = "ajax.googleapis.com/ajax/services/language/translate?v=1.0&langpair=$text_language%7C$disp_language&q=".urlencode($text);
    
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_REFERER, "www.envaya.org");    
    
    // TODO referrer
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
    
    $text = str_replace(",;", "\n", $text);
    
    $trans = new Translation();    
    $trans->owner_guid = 0;
    $trans->container_guid = 0;
    $trans->access_id = ACCESS_PUBLIC;
    $trans->save();
    $trans->key = $key;
    $trans->text = $text;
    
    return $trans;
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
        'interests' => 'tags',
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

register_elgg_event_handler('init','system','envaya_init');
