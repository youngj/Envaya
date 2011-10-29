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
    echo "<td style='width:150px;text-align:right;font-size:10px;white-space:nowrap'>";
    echo view('input/checkboxes', array(
        'name' => 'share_links',
        'options' => array(
            'email' => __('share:email'),
            'facebook' => __('share:facebook'),
            'twitter' => __('share:twitter'),
        ),        
        'value' => $user->get_design_setting('share_links'),
        'after_label' => true,
    )); 
    echo "</td>";
    echo "</tr></table>";
