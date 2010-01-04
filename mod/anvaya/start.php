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

function pluginname_init() {

	org_fields_setup();

	// Register a page handler, so we can have nice URLs
	register_page_handler('org','org_page_handler');

	register_entity_type('group', 'organization');
   	// This operation only affects the db on the first call for this subtype
   	// If you change the class name, you'll have to hand-edit the db
   	add_subtype('group', 'organization', 'Organization');

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
		set_input('org_guid', $page[0]);
		include(dirname(__FILE__) . "/orgprofile.php");
	}
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
register_elgg_event_handler('init','system','pluginname_init');


?>