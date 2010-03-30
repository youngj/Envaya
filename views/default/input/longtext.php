<?php

	/**
	 * Elgg long text input
	 * Displays a long text input field
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * 
	 */

	$class = @$vars['class'] ?: "input-textarea";
    
    $setDirty = (@$vars['trackDirty']) ? " onchange='setDirty(true)'" : "";

    $value = restore_input($vars['internalname'], @$vars['value']); 
	
?>

<textarea class="<?php echo $class; ?>" name="<?php echo $vars['internalname']; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> <?php if (@$vars['disabled']) echo ' disabled="yes" '; ?> <?php echo @$vars['js'], $setDirty; ?>><?php echo escape($value); ?></textarea> 