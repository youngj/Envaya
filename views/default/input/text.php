<?php
	/**
	 * A text input field
	 */
	    
    $name = null;            // html name attribute for input field
    $value = null;           // html value attribute
    $track_dirty = false;    // call setDirty when the field is changed?    
    extract($vars);
    
    $attrs = Markup::get_attrs($vars, array(
        'type' => 'text',           // these standard attribute names can also be passed in $vars
        'class' => 'input-text',
        'maxlength' => null,
        'name' => null,
        'style' => null,
        'id' => null,
    ));

    $attrs['value'] = restore_input($name, $value, $track_dirty); 
    
    if ($track_dirty)
    {
        $attrs['onkeypress'] = "trackDirty(event)";
    }       

    echo Markup::empty_tag('input', $attrs);    
