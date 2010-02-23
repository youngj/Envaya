<?php

function view_translated($org, $text)
{        
    $differentLanguage = ($org->language != get_language());

    if ($differentLanguage && $text)
    {
        $translate = true || get_input("translate");
    
        if ($translate)
        {
            $translation = lookup_translation($text, $org->language);            
            if ($translation)
            {
                return elgg_view_entity($translation);
            }
        }
        else
        {
            return elgg_view("output/longtext",array('value' => $text));
        }
    }   

    return elgg_view("output/longtext",array('value' => $text));        
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
            
    $text = html_entity_decode($translated, ENT_QUOTES);
    
    $text = str_replace(",;", "\n", $text);
    
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
    register_page_handler('orgprofile','org_profile_page_handler');
    register_page_handler('org','org_page_handler');
    register_page_handler('login','login_page_handler');

	register_entity_type('user', 'organization');
    
    register_entity_type('object', 'translation');
    
   	// This operation only affects the db on the first call for this subtype
   	// If you change the class name, you'll have to hand-edit the db
   	add_subtype('user', 'organization', 'Organization');
    
    add_subtype('object', 'translation', 'Translation');

    // Register a URL handler
    register_entity_url_handler('org_url','user','organization');
    register_entity_url_handler('blogpost_url','object','blog');

    // Extend system CSS with our own styles
    extend_view('css','org/css');

    // Replace the default index page
    register_plugin_hook('index','system','new_index');
    register_plugin_hook('entity:icon:url', 'user', 'org_icon_hook');

    // Register an annotation handler for comments etc
    register_plugin_hook('entity:annotate', 'object', 'blog_annotate_comments');

}

function envaya_pagesetup()
{
    if (get_context() == "blog" || get_context() == "org")
    {
        $org = page_owner_entity();        

        if (!empty($org) && can_write_to_container(0, $org->guid))
        {
            add_submenu_item(elgg_echo('org:view'),$org->getUrl());
            add_submenu_item(elgg_echo("org:edit"), $org->getUrl() . "/edit");
        	add_submenu_item(elgg_echo('org:mobilesettings'),$org->getUrl()."/mobilesettings");
        	add_submenu_item(elgg_echo('org:editmap'), $org->getUrl() . "/editmap");
            add_submenu_item(elgg_echo('blog:addpost'),$org->getUrl()."/newpost");
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
		        return;
            case "checkmail":
                include(dirname(__FILE__) . "/checkmail.php");
                return;
    		case "browse":
    			set_page_owner(0);
                include(dirname(__FILE__) . "/browseorgs.php");
    		    return;
            case "search":
                include(dirname(__FILE__) . "/search.php");
                return;
	    }
	}
}

function org_profile_page_handler($page)
{                
    $org = get_user_by_username($page[0]);                

    set_input('org_guid', $org->guid);
    set_page_owner($org->guid);
    set_context("org");

    if (isset($page[1]))
    {
        switch ($page[1])
        {
            case "news":
                set_context("blog");
                include(dirname(__FILE__) . "/blog.php");
                return;
            case "newpost";
                include(dirname(__FILE__) . "/newPost.php");
                return;
            case "mobilesettings":
                include(dirname(__FILE__) . "/mobileSettings.php");
                return;
            case "editmap":
                include(dirname(__FILE__) . "/editMap.php");
                return;
            case "post":
                set_context("blog");
                set_input("blogpost", $page[2]);

                switch ($page[3])
                {
                    case "edit":
                        include(dirname(__FILE__) . "/editPost.php");
                        return;
                    default:
                        include(dirname(__FILE__) . "/blogPost.php");
                        return;
                }
            case "edit":
                include(dirname(__FILE__) . "/editOrg.php");
                return;
            case "icon":
                set_input('size', $page[2]);
                include(dirname(__FILE__) . "/icon.php");
                return;                
            default:
                break;
        }
    }
    
    include(dirname(__FILE__) . "/orgprofile.php");
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
	return $CONFIG->url . "{$entity->username}";
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
        return $org->getUrl() . "/post/" . $blogpost->getGUID();
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
		$filehandler->owner_guid = $entity->guid;
		$filehandler->setFilename("envaya/" . $entity->guid . $size . ".jpg");

		if ($filehandler->exists())
		{
			return $CONFIG->url . "{$entity->username}/icon/$size/$icontime.jpg";
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

// register for the init, system event when our plugin start.php is loaded
register_elgg_event_handler('init','system','envaya_init');
register_elgg_event_handler('pagesetup','system','envaya_pagesetup');

register_action("org/add",false,dirname(__FILE__) . "/actions/addOrg.php");
register_action("org/edit",false,dirname(__FILE__) . "/actions/editOrg.php");
register_action("org/delete",false,dirname(__FILE__) . "/actions/deleteOrg.php");
register_action("org/approve",false,dirname(__FILE__) . "/actions/approveOrg.php");
register_action("org/verify",false,dirname(__FILE__) . "/actions/verifyOrg.php");
register_action("org/changeEmail", true,dirname(__FILE__). "/actions/changeEmail.php");
register_action("org/editMap",false,dirname(__FILE__) . "/actions/editMap.php");
register_action("changeLanguage", true,dirname(__FILE__). "/actions/changeLanguage.php");
register_action("news/add",false,dirname(__FILE__) . "/actions/addPost.php");
register_action("news/edit",false,dirname(__FILE__) . "/actions/editPost.php");
register_action("news/delete",false,dirname(__FILE__) . "/actions/deletePost.php");



?>