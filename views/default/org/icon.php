<?php
    $org = null;
    $size = 'small';
    extract($vars);
    
    $icon_props = $org->get_icon_props($size);   

    $attrs = Markup::get_attrs($vars, array(
        'class' => null,
        'id' => null,
        'style' => null,
        'src' => $icon_props['url'],
        'alt' => $org->name,
        'width' => $icon_props['width'],
        'height' => $icon_props['height'],
        'border' => '0',
    ));
    
    echo "<img ".Markup::render_attrs($attrs)." />";