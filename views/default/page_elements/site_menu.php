<?php    
    $submenu = get_submenu_group('topnav', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group'); 
    if (!empty($submenu))
    {
        echo "<div id='site_menu'>$submenu<div style='clear:both'></div></div>";
    }    
    else
    {
        echo "<div id='no_site_menu'></div>";
    }
