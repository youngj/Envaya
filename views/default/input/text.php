<?php

	/**
	 * Displays a text input field
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * @uses $vars['disabled'] If true then control is read-only
	 * @uses $vars['class'] Class override
	 */
	
	$class = @$vars['class'];
	if (!$class) $class = "input-text";
    
    $setDirty = (@$vars['trackDirty']) ? " onchange='setDirty(true)'" : "";
    
    $value = restore_input($vars['internalname'], @$vars['value']); 
?>

<input type="text" <?php if (@$vars['disabled']) echo ' disabled="yes" '; ?> <?php echo @$vars['js'], $setDirty; ?> name="<?php echo $vars['internalname']; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> value="<?php echo escape($value); ?>" class="<?php echo $class ?>"/> 