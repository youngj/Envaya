<?php   
   if (!@$vars['no_top_bar']) 
   {    
?>
<div id="topbar" style='font-size:small;'><a style='color:white' href="/home">Envaya</a></div>
<?php
$submenuB = implode(' ', PageContext::get_submenu('edit')->get_items());
if ($submenuB)
{
    echo "<div id='edit_submenu'>$submenuB</div>";
}
?>
<?php
    }
?>