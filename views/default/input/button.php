<?php
    /**
     * An input button, by default a submit button
     */

    $value = null;
    $track_dirty = false;
    extract($vars);
     
    $attrs = Markup::get_attrs($vars, array(
        'class' => 'submit_button',
        'type' => 'submit',
        'name' => null,
        'style' => null,
        'id' => null,
        'value' => '1'
    ));    
        
    if ($track_dirty && !isset($attrs['onclick']))
    {
        $attrs['onclick'] = 'setSubmitted()';
    }
    
    echo "<button ".Markup::render_attrs($attrs)."><div><span>".escape($value)."</span></div></button>";
    