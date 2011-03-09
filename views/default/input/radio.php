<?php

    /**
     * Displays a radio input field
     *
     * @uses $vars['value'] The current value, if any
     * @uses $vars['js'] Any Javascript to enter into the input tag
     * @uses $vars['name'] The name of the input field
     * @uses $vars['options'] An array of strings representing the options for the radio field as "label" => option
     *
     */

    $class = @$vars['class'];
    if (!$class) $class = "input-radio";

    $vars['value'] = restore_input($vars['name'], @$vars['value']);

    $js = @$vars['js'] ?: '';

    $br = @$vars['inline'] ? '' : '<br />';
    $labelClass = @$vars['inline'] ? ' optionLabelInline' : '';

    foreach($vars['options'] as $option => $label) {
        if (strtolower($option) != strtolower($vars['value'])) {
            $selected = "";
        } else {
            $selected = "checked = \"checked\"";
        }

        $id = (isset($vars['id'])) ? "id=\"{$vars['id']}\"" : '';
        $disabled = (@$vars['disabled']) ? ' disabled="yes" ' : '';
        $onclick = (@$vars['trackDirty']) ? "onclick='javascript:setDirty(true)' " : '';
             
        echo "<label class='optionLabel$labelClass'><input type=\"radio\" $disabled{$onclick} {$js} name=\"{$vars['name']}\" $id value=\"".escape($option)."\" {$selected} class=\"$class\" />{$label}</label>$br";
    }

?>