<?php

	/**
	 * Displays a text input field
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['name'] The name of the input field
	 * @uses $vars['disabled'] If true then control is read-only
	 * @uses $vars['class'] Class override
	 */
	
	$class = @$vars['class'];
	if (!$class) $class = "input-text";
    
    $setDirty = (@$vars['trackDirty']) ? " onchange='setDirty(true)'" : "";
    
    $value = restore_input($vars['name'], @$vars['value']); 
?>

<input type="text" <?php if (@$vars['disabled']) echo ' disabled="yes" '; ?> <?php echo @$vars['js'], $setDirty; ?> name="<?php echo $vars['name']; ?>" <?php if (isset($vars['id'])) echo "id=\"{$vars['id']}\""; ?> value="<?php echo escape($value); ?>" class="<?php echo $class ?>"/> 