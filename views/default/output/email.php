<?php

	/**
	 * Displays an email address that was entered using an email input field
	 * 
	 * @uses $vars['value'] The email address to display
	 * 
	 */

    if (!empty($vars['value'])) {
    	echo "<a href=\"mailto:" . $vars['value'] . "\">". escape($vars['value']) ."</a>";
    }
?>