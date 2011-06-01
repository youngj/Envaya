<?php

    /**
     * A collection of radio inputs
     */

    $name = null;           // html name attribute for input field
    $value = null;          // html value attribute
    $track_dirty = false;    // call setDirty when the field is changed?    
    $options = null;        // associative array of value => text pairs
    $inline = false;
    extract($vars);
     
    $attrs = Markup::get_attrs($vars, array(
        'type' => 'radio',
        'class' => 'input-radio',
        'style' => null,
        'id' => null,
        'name' => null,
    ));
    
    if ($track_dirty)
    {
        $attrs['onchange'] = "setDirty(true)";
    }      

    $value = restore_input($name, $value, $track_dirty);          

    $br = $inline ? '' : '<br />';
    $labelClass = $inline ? ' optionLabelInline' : '';

    foreach ($options as $option_value => $option_text) 
    {
        $option_attrs = $attrs;
        
        if ($option_value == $value)
        {
            $option_attrs['checked'] = 'checked';
        }
        $option_attrs['value'] = $option_value;
             
        echo "<label class='optionLabel$labelClass'>";
        echo "<input ".Markup::render_attrs($option_attrs)." />";
        echo "{$option_text}</label>$br";
    }

?>