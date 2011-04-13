<?php
    $org = $vars['org'];
    $icon_props = $org->get_icon_props(@$vars['size'] ?: 'small');   
    $alt = escape($org->name);
    
    echo "<img src='".escape($icon_props['url'])."' alt='$alt' width='".escape($icon_props['width'])."' height='".escape($icon_props['height'])."' border='0' ".(@$vars['js'])." />";