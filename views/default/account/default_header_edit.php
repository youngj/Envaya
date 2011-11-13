<?php        
    $user = $vars['user'];
    
    echo "<table id='heading' style='width:100%'><tr>";            
    echo "<td style='width:80px'><a href='javascript:void(0)'>";
    echo view('account/icon', array('user' => $user, 'size' => 'medium'));
    echo "</a></td>";       
    echo "<td>";
    echo "<h2 class='withicon'>".escape($user->name)."</h2>";
    echo "<h3 class='withicon'>";
    echo view('input/text', array(
        'name' => 'tagline', 
        'style' => "width:290px",
        'value' => $user->get_design_setting('tagline')
    ));
    echo "</h3>";    
    echo "</td>";    
    echo "</tr></table>";
