<?php    
    if (@$vars['args'])
    {
        $class = @$vars['args']['org_only'] ? "org_only_heading" : "";
    }    
    else
    {
        $class = '';
    }
    
    echo "<h1 class='$class'>".escape($vars['title'])."</h1>";    
