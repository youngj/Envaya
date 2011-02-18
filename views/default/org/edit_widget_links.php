<?php
    $org = $vars['org'];

    $widgets = $org->get_available_widgets();

    $widgetList = array();   

    foreach ($widgets as $widget)
    {
        if ($widget->is_active())
        {
            $widgetList[] = "<a href='{$widget->get_edit_url()}?from=pg/dashboard'><span>".
                escape($widget->get_title())."</span></a>";
        }
    }

    echo "<div id='edit_pages_menu'>";
    echo implode(' ', $widgetList);
    echo "</div>";
        