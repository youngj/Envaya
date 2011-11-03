<?php
    $groups = $vars['item_groups'];
    
    echo "<div style='font-size:12px'>";   
    ksort($groups);
    foreach ($groups as $heading => $items)
    {
        sort($items);
        echo $heading." (".sizeof($items).") :<br />";
        foreach ($items as $item)
        {
            echo "&nbsp;&nbsp; $item<br />";
        }
    }
    echo "</div>";