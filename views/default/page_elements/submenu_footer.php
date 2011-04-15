<?php
    if (!empty($vars['submenu']))
    {
        echo "<div class='footerLinks'>".implode(' &middot; ', $vars['submenu'])."</div>";
    }
?>