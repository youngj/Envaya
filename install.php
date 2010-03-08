<?php

	/**
	 * Elgg install script
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

    require_once(dirname(__FILE__) . "/engine/start.php");
   		
	elgg_set_viewtype('failsafe');
	if (is_installed())
    {
	    forward("index.php");
    }    
	else
    {
        run_sql_script(dirname(__FILE__) . "/engine/schema/mysql.sql");
        init_site_secret();
        system_message(elgg_echo("installation:success"));
        datalist_set('installed', 1);  
        system_message(elgg_echo("installation:configuration:success"));
        forward("account/register.php");
    }    

?>