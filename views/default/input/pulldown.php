<?php

    /**
     * Displays a pulldown input field
     *
     * @uses $vars['value'] The current value, if any
     * @uses $vars['js'] Any Javascript to enter into the input tag
     * @uses $vars['internalname'] The name of the input field
     * @uses $vars['options'] An associative array of "value" => "option" where "value" is an internal name and "option" is
     *                               the value displayed on the button. Replaces $vars['options'] when defined.
     */

    $class = @$vars['class'] ?: "input-pulldown";

    $vars['value'] = restore_input(@$vars['internalname'], @$vars['value']);
?>


<select name="<?php echo @$vars['internalname']; ?>" <?php if (isset($vars['internalid'])) echo "id=\"{$vars['internalid']}\""; ?> <?php echo @$vars['js']; ?> <?php if (@$vars['disabled']) echo ' disabled="yes" '; ?> class="<?php echo $class; ?>">
<?php
    if (@$vars['empty_option'])
    {
        echo "<option value=''>".escape($vars['empty_option'])."</option>";
    }

    if ($vars['options'])
    {
        foreach($vars['options'] as $value => $option)
        {
            if ($value != $vars['value'])
            {
                echo "<option value=\"".escape($value)."\">". escape($option) ."</option>";
            } else {
                echo "<option value=\"".escape($value)."\" selected=\"selected\">".escape($option)."</option>";
            }
        }
    }

?>
</select>