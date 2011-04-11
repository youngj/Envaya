<div id="topbar"><a href="/home"><img style='vertical-align:middle' src="/_graphics/logo2.gif" alt="Envaya" width="120" height="25"></a>
<?php
    if (PageContext::get_theme() == 'home') 
    {
        echo "<div style='float:right'>".view('page_elements/login_area', $vars)."</div>";     
    }
?>
<div style='clear:both'></div>
</div>
<?php
$submenuB = PageContext::get_submenu_group('edit', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group');
if ($submenuB)
{
    echo "<div id='edit_submenu'>$submenuB</div>";
}
?>