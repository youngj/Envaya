<?php
    /**
     * Create a hidden data field
     * @uses $vars['value'] The current value, if any
     * @uses $vars['js'] Any Javascript to enter into the input tag
     * @uses $vars['internalname'] The name of the input field
     *
     */

?>
<input type="hidden" <?php echo @$vars['js']; ?> name="<?php echo $vars['internalname']; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> value="<?php echo escape($vars['value']); ?>" />