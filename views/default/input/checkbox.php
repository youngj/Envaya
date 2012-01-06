<?php

    /**
     * One checkbox
     */

    $name = null;           // html name attribute for input field
    $value = 1;             // html value attribute
    $track_dirty = false;    // call setDirty when the field is changed?    
    $after_label = false;   // true if the checkbox goes after the label, false otherwise
    $label = null;
    $checked = false;
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
   
    if ($checked)
    {
        $attrs['checked'] = 'checked';
    }
    $attrs['value'] = $value;
    $attrs['name'] = $name;
           
    $input = Markup::empty_tag('input', $attrs);
    
    $content = $after_label ? escape($label).$input : $input.escape($label);
    
    echo "<label class='optionLabel'>$content</label>";