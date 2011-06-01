<?php

	/**
	 * A file input field
	 */

    $name = null;            // html name attribute for input field
    $value = null;           // html value attribute
    $track_dirty = false;     // call setDirty when the field is changed?    
    extract($vars);
    
    $attrs = Markup::get_attrs($vars, array(
        'type' => 'file',
        'size' => '25',
        'class' => 'input-file',
        'name' => null,
        'style' => null,
        'id' => null,
    ));
    
    echo Markup::empty_tag('input', $attrs);
