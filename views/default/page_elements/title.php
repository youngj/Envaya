<?php    
    if (@$vars['args'])
    {
        $class = @$vars['args']['org_only'] ? "org_only_heading" : "";
    }    
    
    echo "<h1 class='$class'>".escape($vars['title'])."</h1>";    
