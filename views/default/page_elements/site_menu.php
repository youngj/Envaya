<?php    
    $submenu = implode(' ', PageContext::get_submenu()->get_items());
    
    if (!empty($submenu))
    {
        echo "<div id='site_menu'>$submenu<div style='clear:both'></div></div>";
        
        PageContext::add_header_html("<!--[if IE 6]>
<style type='text/css'>
#site_menu_container a {width:10px}
</style>
<![endif]-->");        
    }    
    else
    {
        echo "<div id='no_site_menu'></div>";
    }
