<?php
    $org = $vars['org'];
    $subtitle = @$vars['subtitle'];

    echo "<div class='thin_column'><table id='heading'><tr>";
        
    $link = $org->get_url();
    
    $img = view('org/icon', array('org' => $org, 'size' => 'medium'));
    echo "<td><a href='$link'>$img</a></td>";

    $escTitle = escape($org->name);    
    
    echo "<td>";
    echo "<a href='$link'><h2 class='withicon'>$escTitle</h2></a>";      

    if ($subtitle)
    {
        echo "<h3 class='withicon'>".escape($subtitle)."</h3>";
    }        
    echo "</td>";    
    echo "</tr></table></div>";
