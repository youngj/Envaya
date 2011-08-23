<?php        
    $title = @$vars['title'] ?: '';    
    $escTitle = escape($title);    

    $class = @$vars['org_only'] ? "org_only_heading" : "";

    echo "<div id='heading'>";
    echo "<h1 class='$class'>$escTitle</h1>";    
    echo "</div>";