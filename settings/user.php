<?php
	/**
	 * Elgg user settings functions.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 * @author Curverider Ltd 
	 * @link http://elgg.org/
	 */

	// Get the Elgg framework
		require_once(dirname(dirname(__FILE__)) . "/engine/start.php");

	// Make sure only valid admin users can see this
		gatekeeper();
		
	// Make sure we don't open a security hole ...
		if ((!page_owner_entity()) || (!page_owner_entity()->canEdit())) {
			set_page_owner($_SESSION['guid']);
		}

        $title = elgg_echo("usersettings:user");
        
        set_context('editor');

		page_draw($title,
            elgg_view_layout("one_column_padded", elgg_view_title($title), elgg_view("usersettings/form"))
        );
?>