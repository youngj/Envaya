<?php
    $controls = PageContext::get_submenu('top')->get_items();           
    
    if (sizeof($controls))
    {
        echo "<div id='top_menuc'>";
        echo "<div id='top_menu'>";        
        echo implode(' &nbsp;&middot;&nbsp; ', $controls);
        echo "</div>";
        echo "</div>";
        
        PageContext::add_header_html("
<!--[if lte IE 7]>
<style type='text/css'>
#top_menu { *display:inline; }
</style>
<![endif]-->");
    }     
    else
    {
        echo "<div style='height:1px'></div>";
    }    