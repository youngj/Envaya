<?php
    $widget = $vars['widget'];
    
    echo "<div class='padded' style='padding-bottom:0px;padding-top:0px;'>";
    
    echo view('breadcrumb', array(
        'separator' => ' : ', 
        'include_last' => false,
        'items' => $widget->get_breadcrumb_items())
    );
    echo "<h2>".escape($widget->get_title())."</h2>";
    echo "</div>";