<?php

    $widget = $vars['widget'];

    echo "<div class='section_content' style='padding:0px'>";
    echo view('widgets/generic_view', array('widget' => $widget));    
    
    echo "<div class='section_content padded' style='padding-top:0px'>";    
    echo "<ol>";
    
    $sub_widgets = $widget->query_menu_widgets()->filter();    
    
    foreach ($sub_widgets as $sub_widget)
    {
        echo "<li><a href='{$sub_widget->get_url()}'>".escape($sub_widget->get_title())."</a></li>";
    }

    echo "</ol>";
    echo "</div>";
    echo "</div>";