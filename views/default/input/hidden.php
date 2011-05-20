<?php
    /**
     * Create a hidden data field
     * @uses $vars['value'] The current value, if any
     * @uses $vars['js'] Any Javascript to enter into the input tag
     * @uses $vars['name'] The name of the input field
     *
     */

?>
<input type="hidden" <?php echo @$vars['js']; ?> name="<?php echo $vars['name']; ?>" <?php if (isset($vars['id'])) echo "id=\"{$vars['id']}\""; ?> value="<?php echo escape(@$vars['value']); ?>" />