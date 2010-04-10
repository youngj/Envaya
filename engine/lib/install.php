<?php

	/**
	 * Elgg installation
	 * Various functions to assist with installing and upgrading the system
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */
		
	/**
	 * Returns whether or not other settings have been set
	 *
	 * @return true|false Whether or not the rest of the installation has been followed through with
	 */
		function is_installed() {			
			global $CONFIG;
            try 
            {
			    return datalist_get('installed');			
            }
            catch (DatabaseException $e)
            {
                return false;
            }
		}
		
		
	/**
	 * Initialisation for installation functions
	 *
	 */
    function install_init() {
    }

    register_elgg_event_handler("boot","system","install_init");
		
?>