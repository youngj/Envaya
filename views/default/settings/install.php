<?php

	/**
	 * Elgg system settings on initial installation
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 * 
	 */

	echo "<p>" . autop(__("installation:settings:description")) . "</p>";

	echo view("settings/system",array("action" => "action/systemsettings/install"));

?>