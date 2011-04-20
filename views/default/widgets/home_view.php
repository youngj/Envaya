<?php

    $widget = $vars['widget'];
    
    $sub_widgets = $widget->query_menu_widgets()->filter();    
    
    foreach ($sub_widgets as $sub_widget)
    {
        echo "<div class='section_header'>".escape($sub_widget->get_title())."</div>";
        echo $sub_widget->render_view();
    }
    if (!sizeof($sub_widgets))
    {
        echo view('section', array('content' => sprintf(__('widget:empty'), escape($widget->get_title()))));
    }