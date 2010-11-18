<?php

	/**
	 * Displays a properly formatted date
	 * 
	 * @uses $vars['value'] A UNIX epoch timestamp
	 * 
	 */

    if ($vars['value'] > 86400) {
        echo date("F j, Y",$vars['value']);
    }
?>