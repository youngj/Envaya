<?php    
    $class = @$vars['org_only'] ? "org_only_heading" : "";

    echo "<div id='heading'>";
    echo "<h1 class='$class'>".escape($vars['title'])."</h1>";    
    echo "</div>";
