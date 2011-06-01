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
    echo "<input ".Markup::render_attrs($attrs)." />"; 