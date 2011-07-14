<?php
    $widget = $vars['widget'];
    
    echo "<ol>";
    
    $sub_widgets = $widget->query_menu_widgets()->filter();    
    
    foreach ($sub_widgets as $sub_widget)
    {
        echo "<li>";
        echo "<a href='{$sub_widget->get_url()}'>".escape($sub_widget->get_title())."</a>";
        
        if ($sub_widget instanceof Widget_Menu)
        {
            echo view('widgets/menu_menu', array('widget' => $sub_widget));
        }
        
        echo "</li>";        
    }

    echo "</ol>";