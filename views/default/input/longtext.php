<?php
	/**
	 * A multi-line textarea
     */
     
    $name = null;            // html name attribute for input field
    $value = null;           // html value attribute
    $track_dirty = false;     // call setDirty when the field is changed?    
    extract($vars);
    
    $attrs = Markup::get_attrs($vars, array(
        'class' => 'input-textarea',
        'name' => null,
        'style' => null,
        'id' => null,
    ));

    $value = Input::restore_value($name, $value, $track_dirty); 
    
    if ($track_dirty)
    {
        $attrs['onkeypress'] = "trackDirty(event)";
    }       
    
    echo "<textarea ".Markup::render_attrs($attrs).">".escape($value)."</textarea>";    
