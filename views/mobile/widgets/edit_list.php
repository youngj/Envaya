<?php
    $container = $vars['container'];
    
    $widgets = $container->query_widgets()->filter();    
    $from = escape(urlencode($_SERVER['REQUEST_URI']));    
    $widget_list = array();
    
    foreach ($widgets as $widget)
    {
        ob_start();
        
        echo "<a href='{$widget->get_edit_url()}?from=$from'>".
                escape($widget->get_title())."</a>";
                    
        $widget_list[] = ob_get_clean();        
    }
     
    if (sizeof($widget_list))
    {    
        echo "<div class='widget_list'>";
        echo implode('<br />', $widget_list);
        echo "</div>";
    }
