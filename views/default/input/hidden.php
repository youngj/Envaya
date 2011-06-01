<?php
    /**
     * A hidden form field
     */   
    $attrs = Markup::get_attrs($vars, array(
        'type' => 'hidden',
        'name' => null,
        'value' => null,
        'id' => null,
    ));
    
    echo Markup::empty_tag('input', $attrs);    