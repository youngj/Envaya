<?php

// Class source
class Organization extends ElggGroup {

  protected function initialise_attributes() {
    parent::initialise_attributes();
    $this->attributes['subtype'] = 'organization';

    //Notes:
    // this->isVerifyingOrg
    // verifiedBy... relationship
    // when admin performs verification, he/she chooses from a dropdown of the Orgs he/she is an admin of.  Organization is then verified by this chosen super org
    // entity_relationships
    //addRelationship(...
    
    
    
    /*
    TODO: implement or integrate Enum implementation and use it instead of constants.
    approval values:
    -1 -- Denied approval
    0 -- Awaiting approval
    1 -- Approved
    2 -- Approved by super-admin
    3 -- Verified by partner organization
    4 -- Verified by Envaya host country national
    5 -- Verified by super-admin
    */
  }

  public function __construct($guid = null) {
    parent::__construct($guid);
  }

  // more customizations here

  public function isApproved()
  {
      return $this->approval > 0;
  }

  public function isVerified()
  {
      return $this->approval > 2;
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
    }

    public function getPostEmail()
    {
        if (!$this->email_code)
        {
            $this->generateEmailCode();
        }
        return "post+{$this->email_code}@envaya.org";
    }

  public function userCanSee()
  {
      return ($this->isApproved() || isadminloggedin() || ($this->getOwnerEntity() == get_loggedin_user()));
  }

  public function approveOrg()
  {
      if (isadminloggedin())
      {
          $this->set("approval", 2);
          return true;
      }
      else
      {
          return false;
      }
  }

  public function verifyOrg()
  {
      if (isadminloggedin())
        {
            $this->set("approval", 5);
            return true;
        }
        else
        {
            return false;
        }
  }
}

class Translation extends ElggObject
{
    protected function initialise_attributes() 
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = 'translation';
    }    

    public function getSource()
    {
        return "<a href='http://translate.google.com'>Google Translate</a>";
    }
    
    public function getSubtype() 
    {
        return 'translation';            
    }
    

    public function __construct($guid = null) 
    {
        parent::__construct($guid);
    }      
}

function lookup_translation($text, $text_language)
{
    $disp_language = get_language();
    if ($text_language == $disp_language)
    {
        return null;
    }
    
    $key = $text_language . ":" . $disp_language . ":" . sha1($text);
    
    $translations = get_entities_from_metadata('key', $key, 'object', 'translation'); 
    if (!empty($translations))
    {        
        return $translations[0];
    }
    
    $ch = curl_init(); 
    
    $url = "ajax.googleapis.com/ajax/services/language/translate?v=1.0&langpair=$text_language%7C$disp_language&q=".urlencode($text);
    
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_REFERER, "www.envaya.org");    
    
    // TODO referrer
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    
    $json = curl_exec($ch); 
         
    curl_close($ch);     
    
    $res = json_decode($json);
          
    $translated = $res->responseData->translatedText;
            
    $text = html_entity_decode($translated, ENT_QUOTES);
    
    $trans = new Translation();    
    $trans->owner_guid = 0;
    $trans->container_guid = 0;
    $trans->access_id = 2; //public
    $trans->save();
    $trans->key = $key;
    $trans->text = $text;
    
    return $trans;
}


function envaya_init() {

    global $CONFIG;

	org_fields_setup();

	// Register a page handler, so we can have nice URLs
	register_page_handler('org','org_page_handler');
    register_page_handler('login','login_page_handler');

	register_entity_type('group', 'organization');
    
    register_entity_type('object', 'translation');
    
   	// This operation only affects the db on the first call for this subtype
   	// If you change the class name, you'll have to hand-edit the db
   	add_subtype('group', 'organization', 'Organization');
    
    add_subtype('object', 'translation', 'Translation');

    // Register a URL handler
    register_entity_url_handler('org_url','group','organization');
    register_entity_url_handler('blogpost_url','object','blog');

    // Extend system CSS with our own styles
    extend_view('css','org/css');

    // Replace the default index page
    register_plugin_hook('index','system','new_index');
    register_plugin_hook('entity:icon:url', 'group', 'org_icon_hook');

    // Register an annotation handler for comments etc
    register_plugin_hook('entity:annotate', 'object', 'blog_annotate_comments');

}

function envaya_pagesetup()
{
    if (get_context() == "blog" || get_context() == "org")
    {
        $org = page_owner_entity();

        if (!empty($org) && can_write_to_container(0, $org))
        {
            add_submenu_item(elgg_echo('org:view'),$org->getUrl());
            add_submenu_item(elgg_echo("org:edit"), $org->getUrl() . "edit");
        	add_submenu_item(elgg_echo('org:mobilesettings'),$org->getUrl()."mobilesettings/");
            add_submenu_item(elgg_echo('blog:addpost'),$org->getUrl()."newpost/");
        }
        else if (get_context() == 'blog')
        {
            add_submenu_item(elgg_echo('org:view'),$org->getUrl());
        }
    }
}

function org_title($org, $subtitle)
{
    return elgg_view('page_elements/title', array('title' => $org->name, 'subtitle' => $subtitle));
}

/**
 * Group page handler
 *
 * @param array $page Array of page elements, forwarded by the page handling mechanism
 */
function org_page_handler($page)
{
	global $CONFIG;

	if (isset($page[0]))
	{
	    switch($page[0])
		{
		    case "new":
                include(dirname(__FILE__) . "/neworg.php");
		        break;
            case "checkmail":
                include(dirname(__FILE__) . "/checkmail.php");
                break;                
    		case "browse":
    			set_page_owner(0);
                include(dirname(__FILE__) . "/browseorgs.php");
    		    break;
            case "search":
                include(dirname(__FILE__) . "/search.php");
                break;
            case "icon":
                // The username should be the file we're getting
                if (isset($page[1])) {
                    set_input('org_guid',$page[1]);
                }
                if (isset($page[2])) {
                    set_input('size',$page[2]);
                }

                include(dirname(__FILE__) . "/icon.php");
                break;
    		default:
    		    set_input('org_guid', $page[0]);
    		    set_context("org");

                $org = get_entity($page[0]);
                
                set_page_owner($page[0]);

                if (isset($page[2]))
                {
                    switch ($page[2])
                    {
                        case "blog":
                            set_context("blog");
                            include(dirname(__FILE__) . "/blog.php");
                            break;
                        case "newpost";
                            include(dirname(__FILE__) . "/newPost.php");
                            break;
                        case "mobilesettings":
                        	include(dirname(__FILE__) . "/mobileSettings.php");
                        	break;
                        case "post":
                            set_context("blog");
                            set_input("blogpost", $page[3]);

                            switch ($page[4])
                            {
                                case "edit":
                                    include(dirname(__FILE__) . "/editPost.php");
                                    break;
                                default:
                                    include(dirname(__FILE__) . "/blogPost.php");
                                    break;
                            }
                            break;
                        case "edit":
                            include(dirname(__FILE__) . "/editOrg.php");
                            break;
                        default:
                            include(dirname(__FILE__) . "/orgprofile.php");
                    }
                }
                else
                {
                    include(dirname(__FILE__) . "/orgprofile.php");
                }
                break;
	    }
	}
}

function login_page_handler($page)
{
    include(dirname(__FILE__) . "/login.php");
}

/**
 * Populates the ->getUrl() method for org objects
 *
 * @param ElggEntity $entity File entity
 * @return string File URL
 */
function org_url($entity) {

	global $CONFIG;
	$title = friendly_title($entity->name);

	return $CONFIG->url . "{$entity->guid}/$title/";

}

/**
 * Populates the ->getUrl() method for blog objects
 *
 * @param ElggEntity $blogpost Blog post entity
 * @return string Blog post URL
 */
function blogpost_url($blogpost) {

    global $CONFIG;
    $org = $blogpost->getContainerEntity();

    if ($org)
    {
        return $org->getUrl() . "post/" . $blogpost->getGUID();
    }

}


/**
 * This hooks into the getIcon API and provides nice user icons for users where possible.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function org_icon_hook($hook, $entity_type, $returnvalue, $params)
{
	global $CONFIG;

	if ($params['entity'] instanceof Organization)
	{
		$entity = $params['entity'];
		$type = $entity->type;
		$viewtype = $params['viewtype'];
		$size = $params['size'];

		if ($icontime = $entity->icontime) {
			$icontime = "{$icontime}";
		} else {
			$icontime = "default";
		}

		$filehandler = new ElggFile();
		$filehandler->owner_guid = $entity->owner_guid;
		$filehandler->setFilename("envaya/" . $entity->guid . $size . ".jpg");

		if ($filehandler->exists())
		{
			return $CONFIG->url . "pg/org/icon/{$entity->guid}/$size/$icontime.jpg";
		}
		else
		{
			return $CONFIG->url . "mod/envaya/graphics/default$size.gif";
		}
	}
}


function org_fields_setup()
{
	global $CONFIG;

	$CONFIG->org_fields = array(
		'name' => 'text',
		'description' => 'longtext',
        'language' => 'language',
		//'briefdescription' => 'text',
		'interests' => 'tags',
		'website' => 'url',
		'location' => 'text',
	);
}

function new_index() {

    if (!include_once(dirname(__FILE__) . "/index.php"))
    	return false;

    return true;
}

/**
 * Hook into the framework and provide comments on blog entities.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function blog_annotate_comments($hook, $entity_type, $returnvalue, $params)
{
    $entity = $params['entity'];
    $full = $params['full'];

    if (
        ($entity instanceof ElggEntity) &&  // Is the right type
        ($entity->getSubtype() == 'blog') &&  // Is the right subtype
        ($entity->comments_on!='Off') && // Comments are enabled
        ($full) // This is the full view
    )
    {
        // Display comments
        return elgg_view_comments($entity);
    }

}

// register for the init, system event when our plugin start.php is loaded
register_elgg_event_handler('init','system','envaya_init');
register_elgg_event_handler('pagesetup','system','envaya_pagesetup');

register_action("editOrg",false,dirname(__FILE__) . "/actions/editOrg.php");
register_action("deleteOrg",false,dirname(__FILE__) . "/actions/deleteOrg.php");
register_action("approveOrg",false,dirname(__FILE__) . "/actions/approveOrg.php");
register_action("verifyOrg",false,dirname(__FILE__) . "/actions/verifyOrg.php");
register_action("changeLanguage", true,dirname(__FILE__). "/actions/changeLanguage.php");
register_action("changeEmail", true,dirname(__FILE__). "/actions/changeEmail.php");
register_action("blog/add",false,dirname(__FILE__) . "/actions/addPost.php");
register_action("blog/edit",false,dirname(__FILE__) . "/actions/editPost.php");
register_action("blog/delete",false,dirname(__FILE__) . "/actions/deletePost.php");

?>