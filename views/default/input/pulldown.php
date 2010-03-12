<?php

	/**
	 * Elgg pulldown input
	 * Displays a pulldown input field
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * @uses $vars['options'] An array of strings representing the options for the pulldown field
	 * @uses $vars['options_values'] An associative array of "value" => "option" where "value" is an internal name and "option" is 
	 * 								 the value displayed on the button. Replaces $vars['options'] when defined. 
	 */


	$class = $vars['class'];
	if (!$class) $class = "input-pulldown";
    
    $vars['value'] = preserve_input($vars['internalname'], $vars['value']); 

?>


<select name="<?php echo $vars['internalname']; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> <?php echo $vars['js']; ?> <?php if ($vars['disabled']) echo ' disabled="yes" '; ?> class="<?php echo $class; ?>">
<?php
	if ($vars['empty_option'])
    {
        echo "<option value=''>".escape($vars['empty_option'])."</option>";
    }
    
    if ($vars['options_values'])
	{
		foreach($vars['options_values'] as $value => $option) 
        {
	        if ($value != $vars['value']) 
            {
                echo "<option value=\"".escape($value)."\">". escape($option) ."</option>";
	        } else {
                echo "<option value=\"".escape($value)."\" selected=\"selected\">".escape($option)."</option>";
	        }
	    }
	}
	else
	{
	    foreach($vars['options'] as $option) {
        
	        if ($option != $vars['value']) 
            {
                echo "<option>".escape($option)."</option>";
	        } 
            else 
            {
                echo "<option selected=\"selected\">".escape($option)."</option>";
	        }
	    }
	}
?> 
</select>