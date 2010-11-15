<?php
    $org = $vars['org'];

    $widgets = $org->get_available_widgets();

    $widgetList = array();

    foreach ($widgets as $widget)
    {
        $class = (!$widget->guid) ? 'class="widget_disabled"' : '';
        $widgetList[] .= "<a $class href='{$widget->get_edit_url()}?from=pg/dashboard'><span>".
            escape($widget->get_title())."</span></a>";
    }

    echo "<div id='edit_pages_menu'>".implode(' ', $widgetList)."</div>";