<?php
    $user = $vars['user'];
    
    $items = PageContext::get_submenu('user_actions')->get_items();    
    if ($items)
    {
        echo "<div class='adminBox'>".implode(' ', $items)."</div>";
    }

    echo SessionMessages::view_all();