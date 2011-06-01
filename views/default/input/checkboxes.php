<?php

    /**
     * A set of checkboxes
     */

    $name = null;           // html name attribute for input field
    $value = null;          // html value attribute
    $track_dirty = false;    // call setDirty when the field is changed?    
    $after_label = false;   // true if the checkbox goes after the label, false otherwise
    $options = null;        // associative array of value => text pairs
    $columns = 1;           // number of columns to display the checkboxes in
    extract($vars);
     
    $attrs = Markup::get_attrs($vars, array(
        'type' => 'checkbox',
        'class' => 'input-checkboxes',
        'style' => null,
        'id' => null,
    ));

    if ($track_dirty)
    {
        $attrs['onchange'] = "setDirty(true)";
    }    
    
    $value = restore_input($name, $value);          

    $valIsArray = is_array($value);

    $checkboxes = array();
    
    foreach ($options as $option_value => $option_text)
    {
        if ($valIsArray)
        {
            $isSelected = in_array($option_value, $value);
        }
        else
        {
            $isSelected = ($option_value == $value);
        }

        $option_attrs = $attrs;
        
        if ($isSelected)
        {
            $option_attrs['checked'] = 'checked';
        }
        $option_attrs['value'] = $option_value;
        $option_attrs['name'] = "{$name}[]";
               
        $input = Markup::empty_tag('input', $option_attrs);
        
        $content = $after_label ? escape($option_text).$input : $input.escape($option_text);
        
        $checkboxes[] = "<label class='optionLabel'>$content</label><br />";
    }
    
    if ($columns <= 1)
    {
        echo implode('',$checkboxes);
    }
    else
    {
        $start = 0;
        echo "<table>";
        echo "<tr>";
        for ($i = 0; $i < $columns; $i++)
        {
            $end = (int) (($i + 1) * sizeof($checkboxes) / $columns);
            echo "<td style='".(($i > 0) ? 'padding-left:15px' : '')."'>";
            echo implode('', array_slice($checkboxes, $start, $end - $start));
            echo "</td>";
            $start = $end;
        }        
        echo "</tr>";
        echo "</table>";
    }

?>