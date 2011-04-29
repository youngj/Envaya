<?php        
    $logo = @$vars['logo'];
    $title = $vars['title'];    
    $escTitle = escape($title);    
    
    if ($logo)
    {
        $url = escape(@$vars['title_url']);
        echo "<table id='heading'><tr>";
            
        echo "<td><a href='$url'>$logo</a></td>";       
        echo "<td>";
        echo "<h2 class='withicon'><a href='$url'>".escape($vars['sitename'])."</a></h2>";

        if ($title)
        {
            echo "<h3 class='withicon'>$escTitle</h3>";
        }        
        echo "</td>";    
        echo "</tr></table>";
    }
    else
    {
        $class = @$vars['org_only'] ? "org_only_heading" : "";

        echo "<div id='heading'>";
        echo "<h1 class='$class'>$escTitle</h1>";    
        echo "</div>";
    }