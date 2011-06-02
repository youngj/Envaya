<?php

    require_once("scripts/cmdline.php");
    require_once("start.php");
    
    foreach (Widget::query()->where('subclass = ?', 'Home')->filter() as $widget)
    {
        $mission = $widget->get_widget_by_class('Mission');       
        if (!$mission->content)
        {
            $mission->content = $widget->content;
            $mission->data_types = $widget->data_types;
            $mission->save();
        }
                
        $widget->get_widget_by_class('WidgetHandler_Updates')->save();        
        $widget->get_widget_by_class('WidgetHandler_Sectors')->save();
        
        $location = $widget->get_widget_by_class('WidgetHandler_Location');       
        if (!$location->zoom)
        {
            $location->zoom = $widget->zoom;
        }
        $location->save();
    }