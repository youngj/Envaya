<?php    
    $submenu = implode(' ', PageContext::get_submenu()->render_items());
    
    if (!empty($submenu))
    {
        echo "<div id='site_menu'>$submenu<div style='clear:both'></div></div>";
    }    
    else
    {
        echo "<div id='no_site_menu'></div>";
    }
