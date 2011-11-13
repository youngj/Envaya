<?php
    $escUrl = escape($vars['site_url']);
    
    echo "<table id='heading' style='width:100%'><tr>";    

    $logo = $vars['logo'];
    if ($logo)
    {
        echo "<td style='width:80px'><a href='$escUrl'>$logo</a></td>";       
    }
    echo "<td>";
    echo "<h2 class='withicon'><a href='$escUrl'>".escape($vars['site_name'])."</a></h2>";
    
    if ($vars['tagline'])
    {
        echo "<h3 class='withicon'>".escape($vars['tagline'])."</h3>";
    }        
    echo "</td>";                          
    echo "</tr></table>";