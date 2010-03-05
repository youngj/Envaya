<?php

	/**
	 * Elgg version library.
	 * Contains code for handling versioning and upgrades.
	 * 
	 * @package Elgg
	 * @subpackage Core


	 * @link http://elgg.org/
	 */

	/**
	 * Get the current version information
	 *
	 * @param true|false $humanreadable Whether to return a human readable version (default: false)
	 * @return string|false Depending on success
	 */
		function get_version($humanreadable = false) {
			
			global $CONFIG;
			if (@include($CONFIG->path . "version.php")) {
				if (!$humanreadable) return $version;
				return $release;
			}
			
			return false;
			
		}		
				
?>