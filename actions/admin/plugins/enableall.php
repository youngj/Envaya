<?php
	/**
	 * Enable plugin action.
	 *
	 * @package Elgg
	 * @subpackage Core
	 * @author Curverider Ltd
	 * @link http://elgg.org/
	 */

	require_once(dirname(dirname(dirname(__DIR__))) . "/engine/start.php");

	// block non-admin users
	admin_gatekeeper();

	// Validate the action
	action_gatekeeper();

	$plugins = get_installed_plugins();

	foreach ($plugins as $p => $data)
	{
		// Enable
		if (enable_plugin($p))
			system_message(sprintf(elgg_echo('admin:plugins:enable:yes'), $p));
		else
			register_error(sprintf(elgg_echo('admin:plugins:enable:no'), $p));
	}

	// Regen view cache
	elgg_view_regenerate_simplecache();

	forward($_SERVER['HTTP_REFERER']);
	exit;

?>