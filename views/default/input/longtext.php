<?php

	/**
	 * Displays a long text input field
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['name'] The name of the input field
	 * 
	 */

	$class = @$vars['class'] ?: "input-textarea";
    
    $setDirty = (@$vars['trackDirty']) ? " onchange='setDirty(true)'" : "";

    $value = restore_input(@$vars['name'], @$vars['value'], @$vars['trackDirty']); 
	
?>

<textarea class="<?php echo $class; ?>" name="<?php echo @$vars['name']; ?>" <?php if (isset($vars['id'])) echo "id=\"{$vars['id']}\""; ?> <?php if (@$vars['disabled']) echo ' disabled="yes" '; ?> <?php echo @$vars['js'], $setDirty; ?>><?php echo escape($value); ?></textarea> 