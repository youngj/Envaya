<?php
	/**
	 * Create a submit input button	 
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * @uses $vars['type'] Submit or reset, defaults to submit.
	 * 
	 */

    $vars['class'] = (isset($vars['class'])) ? $vars['class'] : "submit_button";
?>
<input type='submit' name="<?php echo @$vars['internalname']; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> class="<?php echo $class; ?>" <?php echo @$vars['js']; ?> value='<?php echo escape(@$vars['value']) ?>' />