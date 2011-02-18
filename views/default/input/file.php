<?php

	/**
	 * Displays a file input field
	 * 
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['name'] The name of the input field
	 * 
	 */

    if (!empty($vars['value'])) {
        echo __('fileexists') . "<br />";
    }

    $class = $vars['class'];
	if (!$class) $class = "input-file";
?>
<input type="file" size="25" <?php echo $vars['js']; ?> name="<?php echo $vars['name']; ?>" <?php if (isset($vars['id'])) echo "id=\"{$vars['id']}\""; ?> <?php if ($vars['disabled']) echo ' disabled="yes" '; ?> class="<?php echo $class; ?>" />