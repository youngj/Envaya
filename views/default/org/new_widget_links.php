<?php
    $org = $vars['org'];

    $widgets = $org->get_available_widgets();

    $newWidgetList = array();

    foreach ($widgets as $widget)
    {
        if (!$widget->is_active())
        {
            $newWidgetList[] = "<a href='{$widget->get_edit_url()}?from=pg/dashboard'><span>".
                    escape($widget->get_title())."</span></a>";
        }
    }

    echo "<div id='new_pages_menu'>";   
    echo implode('<br />', $newWidgetList);
    echo "<div><a href='{$org->get_url()}/add_page?from=pg/dashboard'>".__('widget:add_link')."</a></div>";
    echo "</div>";