<div id="topbar"><a href="/home"><img style='display:block' src="/_graphics/logo.gif?v5" alt="Envaya" width="145" height="30"></a></div>
<?php
$submenuB = get_submenu_group('edit', 'canvas_header/link_submenu', 'canvas_header/basic_submenu_group');
if ($submenuB)
{
    echo "<div id='edit_submenu'>$submenuB</div>";
}
?>