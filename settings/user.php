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
		require_once(dirname(__DIR__) . "/engine/start.php");

	// Make sure only valid admin users can see this
		gatekeeper();

	// Make sure we don't open a security hole ...
		if ((!page_owner_entity()) || (!page_owner_entity()->canEdit())) {
			set_page_owner($_SESSION['guid']);
		}

        $title = elgg_echo("usersettings:user");

        set_theme('editor');
        set_context('editor');

        $user = get_loggedin_user();

		page_draw($title,
            elgg_view_layout("one_column", elgg_view_title($title), elgg_view("usersettings/form"))
        );
?>