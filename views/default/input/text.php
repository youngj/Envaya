<?php

	/**
	 * Elgg text input
	 * Displays a text input field
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * @uses $vars['disabled'] If true then control is read-only
	 * @uses $vars['class'] Class override
	 */
	
	$class = $vars['class'];
	if (!$class) $class = "input-text";
    
    $value = preserve_input($vars['internalname'], $vars['value']);	
?>

<input type="text" <?php if ($vars['disabled']) echo ' disabled="yes" '; ?> <?php echo $vars['js']; ?> name="<?php echo $vars['internalname']; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> value="<?php echo escape($value); ?>" class="<?php echo $class ?>"/> 