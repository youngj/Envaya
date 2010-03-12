<?php

	/**
	 * Elgg checkbox input
	 * Displays a checkbox input field
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * @uses $vars['options'] An array of strings representing the label => options for the checkbox field
	 * 
	 */

	$class = $vars['class'];
	if (!$class) $class = "input-checkboxes";

    $vars['value'] = preserve_input($vars['internalname'], $vars['value']); 

    $valIsArray = is_array($vars['value']);
    
    if ($valIsArray)
    {
        $valarray = $vars['value'];
        $valarray = array_map('strtolower', $valarray);
    }    
    else
    {
        $val = strtolower($vars['value']);
    }
	
    foreach($vars['options'] as $option => $label) 
    {
        if ($valIsArray) 
        {
        	$isSelected = in_array(strtolower($option),$valarray);
        } 
        else 
        {
            $isSelected = (strtolower($option) == $val);
        }
        
        $selected = ($isSelected) ? "checked = \"checked\"" : "";
                        
        if (isset($vars['internalid'])) 
            $id = "id=\"{$vars['internalid']}\""; 
        
        $disabled = "";
        if ($vars['disabled']) 
            $disabled = ' disabled="yes" '; 
        
        echo "<label class='optionLabel'><input type=\"checkbox\" $id $disabled {$vars['js']} name=\"{$vars['internalname']}[]\" value=\"".escape($option)."\" {$selected} class=\"$class\" />{$label}</label><br />";
    }

?> 