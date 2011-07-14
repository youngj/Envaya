<?php

    $widget = $vars['widget'];

    echo "<div class='section_content' style='padding:0px'>";
    echo view('widgets/generic_view', array('widget' => $widget));    
    
    echo "<div class='section_content padded' style='padding-top:0px'>";    
    
    echo view('widgets/menu_menu', array('widget' => $widget));

    echo "</div>";
    echo "</div>";