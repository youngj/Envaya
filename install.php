<?php

	/**
	 * Elgg install script
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

	/**
	 * Start the Elgg engine
	 */
		require_once(dirname(__FILE__) . "/engine/start.php");
		global $CONFIG;
		
		elgg_set_viewtype('failsafe');
	/**
	 * If we're installed, go back to the homepage
	 */
		if ((is_installed() && is_db_installed() && datalist_get('installed')))
			forward("index.php");
		
	/**
	 * Install the database
	 */
		if (!is_db_installed()) {
			validate_platform();
			run_sql_script(dirname(__FILE__) . "/engine/schema/mysql.sql");
			init_site_secret();
			system_message(elgg_echo("installation:success"));
            datalist_set('installed', 1);  
            system_message(elgg_echo("installation:configuration:success"));
            forward("account/register.php");
		}
		
    forward("index.php");

?>