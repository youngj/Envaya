<?php
    $user = null;
    $size = 'small';
    extract($vars);
    
    $icon_props = $user->get_icon_props($size);   
    
    $attrs = Markup::get_attrs($vars, array(
        'class' => null,
        'id' => null,
        'style' => null,
        'src' => $icon_props['url'],
        'alt' => $user->name,
        'width' => $icon_props['width'],
        'height' => $icon_props['height'],
        'border' => '0',
    ));

    echo Markup::empty_tag('img', $attrs);        
    