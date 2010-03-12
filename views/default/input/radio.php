<?php

	/**
	 * Elgg radio input
	 * Displays a radio input field
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * @uses $vars['options'] An array of strings representing the options for the radio field as "label" => option
	 * 
	 */
	
	$class = $vars['class'];
	if (!$class) $class = "input-radio";
    
    $vars['value'] = preserve_input($vars['internalname'], $vars['value']); 

    foreach($vars['options'] as $option => $label) {
        if (strtolower($option) != strtolower($vars['value'])) {
            $selected = "";
        } else {
            $selected = "checked = \"checked\"";
        }

        if (isset($vars['internalid'])) $id = "id=\"{$vars['internalid']}\""; 
        if ($vars['disabled']) $disabled = ' disabled="yes" '; 
        echo "<label class='optionLabel'><input type=\"radio\" $disabled {$vars['js']} name=\"{$vars['internalname']}\" $id value=\"".htmlentities($option, ENT_QUOTES, 'UTF-8')."\" {$selected} class=\"$class\" />{$label}</label><br />";
    }

?> 