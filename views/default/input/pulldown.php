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
    $value = restore_input($name, $value);     

    echo "<select ".Markup::render_attrs($attrs).">";

    if (isset($empty_option))
    {
        echo "<option value=''>".escape($empty_option)."</option>";
    }

    if ($options)
    {
        foreach ($options as $option_value => $option_text)
        {
            $option_attrs = array('value' => $option_value);                    
            if ($option_value == $value)
            {
                $option_attrs['selected'] = 'selected';
            }            
            echo "<option ".Markup::render_attrs($option_attrs).">".escape($option_text)."</option>";
        }
    }
?>
</select>