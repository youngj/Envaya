<div id="topbar"><a href="/home"><img style='vertical-align:middle' src="/_media/images/logo2.gif" alt="Envaya" width="120" height="25"></a>
<?php
    if (@$vars['theme_name'] == 'home') 
    {
        echo "<div style='float:right'>".view('page_elements/login_area', $vars)."</div>";     
    }
?>
<div style='clear:both'></div>
</div>
<?php
$submenuB = PageContext::get_submenu('edit')->render();
if ($submenuB)
{
    echo "<div id='edit_submenu'>$submenuB</div>";
}
?>