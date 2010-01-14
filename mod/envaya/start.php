<?php

// Class source
class Organization extends ElggGroup {

  protected function initialise_attributes() {
    parent::initialise_attributes();
    $this->attributes['subtype'] = 'organization';
    
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

function envaya_init() {

    global $CONFIG;

	org_fields_setup();

	// Register a page handler, so we can have nice URLs
	register_page_handler('org','org_page_handler');
    register_page_handler('login','login_page_handler');

	register_entity_type('group', 'organization');
   	// This operation only affects the db on the first call for this subtype
   	// If you change the class name, you'll have to hand-edit the db
   	add_subtype('group', 'organization', 'Organization');

    // Register a URL handler
    register_entity_url_handler('org_url','group','organization');

    // Extend system CSS with our own styles
    extend_view('css','org/css');

    // Replace the default index page
    register_plugin_hook('index','system','new_index');

    register_plugin_hook('entity:icon:url', 'group', 'org_icon_hook');

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
    		case "browse":
    		    set_context('org');
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
    		    include(dirname(__FILE__) . "/orgprofile.php");
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

	return $CONFIG->url . "pg/org/{$entity->guid}/$title/";

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

// register for the init, system event when our plugin start.php is loaded
register_elgg_event_handler('init','system','envaya_init');

register_action("editOrg",false,dirname(__FILE__) . "/actions/editOrg.php");
register_action("deleteOrg",false,dirname(__FILE__) . "/actions/deleteOrg.php");
register_action("approveOrg",false,dirname(__FILE__) . "/actions/approveOrg.php");
register_action("verifyOrg",false,dirname(__FILE__) . "/actions/verifyOrg.php");
register_action("changeLanguage", true,dirname(__FILE__). "/actions/changeLanguage.php");

?>