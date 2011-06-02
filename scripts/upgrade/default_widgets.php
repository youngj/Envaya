<?php

    require_once("scripts/cmdline.php");
    require_once("start.php");

    foreach (Widget::$default_widgets as $widget_name => $props)
    {
        $menu_order = $props['menu_order'];
        $handler_class = $props['handler_class'];
        
        echo "$widget_name $menu_order $handler_class\n";
                
        Database::update('UPDATE widgets set menu_order = ? where widget_name = ? AND (menu_order = 0 or menu_order is null)', 
            array($menu_order, $widget_name));

        Database::update("UPDATE widgets set handler_class = ? where widget_name = ? AND (handler_class = '' or handler_class is null)", 
            array($handler_class, $widget_name));            
    }

    Database::update("UPDATE widgets set handler_class = ? where (handler_class = '' or handler_class is null)", 
            array('WidgetHandler_Generic'));            

    Database::update("UPDATE widgets set menu_order = ? where (menu_order = 0 or menu_order is null)", 
            array(1000));            
            