<?php

    /**
     * Displays an email input field
     *
     * @uses $vars['value'] The current value, if any
     * @uses $vars['js'] Any Javascript to enter into the input tag
     * @uses $vars['name'] The name of the input field
     *
     */

    $class = @$vars['class'];
    if (!$class) $class = "input-text";

    $value = restore_input($vars['name'], @$vars['value']);
?>

<input type="text" <?php echo @$vars['js']; ?> name="<?php echo $vars['name']; ?>" <?php if (isset($vars['id'])) echo "id=\"{$vars['id']}\""; ?>value="<?php echo escape($value); ?>" class="<?php echo $class; ?>"/>