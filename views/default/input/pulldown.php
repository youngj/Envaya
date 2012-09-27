<?php
    /**
     * A pulldown html select box
     */

    $name = null;           // html name attribute for input field
    $value = null;          // html value attribute
    $empty_option = null;   // text for an extra option with value ''
    $options = null;        // associative array of value => text pairs
    extract($vars);
     
    $attrs = Markup::get_attrs($vars, array(
        'class' => 'input-pulldown',
        'name' => null,
        'style' => null,
        'id' => null,
    ));

    $value = Input::restore_value($name, $value);     

    echo "<select ".Markup::render_attrs($attrs).">";

    if (isset($empty_option))
    {
        echo "<option value=''>".
            htmlspecialchars($empty_option, ENT_QUOTES, 'UTF-8')
            ."</option>";
    }

    $is_empty_value = (empty($value) && !is_numeric($value)); // avoid 0/'0' being treated same as '',null
    
    if ($options)
    {
        foreach ($options as $option_value => $option_text)
        {
            $option_attrs = 'value="'.htmlspecialchars($option_value, ENT_QUOTES, 'UTF-8').'"';
            if (!$is_empty_value && $option_value == $value)
            {
                $option_attrs .= ' selected="selected"';
            }            
            echo "<option $option_attrs>".htmlspecialchars($option_text, ENT_QUOTES, 'UTF-8')."</option>";
        }
    }
?>
</select>
