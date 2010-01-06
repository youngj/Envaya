<?php

// Class source
class Organization extends ElggGroup {

  protected function initialise_attributes() {
    parent::initialise_attributes();
    $this->attributes['subtype'] = 'organization';
  }

  public function __construct($guid = null) {
    parent::__construct($guid);
  }

  // more customizations here
}

function envaya_init() {
    
    global $CONFIG;

    add_menu(elgg_echo('Organizations'), $CONFIG->wwwroot . "pg/org/browse/");
    

	org_fields_setup();

	// Register a page handler, so we can have nice URLs
	register_page_handler('org','org_page_handler');
    
	register_entity_type('group', 'organization');
   	// This operation only affects the db on the first call for this subtype
   	// If you change the class name, you'll have to hand-edit the db
   	add_subtype('group', 'organization', 'Organization');

    // Register a URL handler
    register_entity_url_handler('org_url','group','organization');

    // Extend system CSS with our own styles
    //extend_view('css','pluginname/css');

    // Replace the default index page
    //register_plugin_hook('index','system','new_index');
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
		        include($CONFIG->pluginspath . "anvaya/neworg.php");
		    break;
    		case "browse":
    		    set_context('org');
    			set_page_owner(0);
    			include($CONFIG->pluginspath . "anvaya/browseorgs.php");
    			//include($CONFIG->pluginspath . "anvaya/index.php");
    		break;
    		default:
    		    set_input('org_guid', $page[0]);
    		    include(dirname(__FILE__) . "/orgprofile.php");
    		break;
	    }
	}
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

function org_fields_setup()
{
	global $CONFIG;

	$CONFIG->org_fields = array(
		'name' => 'text',
		'description' => 'longtext',
		'briefdescription' => 'text',
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

global $CONFIG;
register_action("editOrg",false,$CONFIG->pluginspath . "anvaya/actions/editOrg.php");
register_action("deleteOrg",false,$CONFIG->pluginspath . "anvaya/actions/deleteOrg.php");

?>