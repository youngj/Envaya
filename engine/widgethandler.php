<?php

/*
 * Interface for viewing/editing a specific type of Widget;
 * see subclasses defined in widgethandler/.
 */
abstract class WidgetHandler
{
    abstract function view($widget);
    abstract function edit($widget);
    abstract function save($widget);
    
    function get_default_title($widget)
    {
        $key = "widget:{$widget->widget_name}";
        $title = __($key);
        return ($title != $key) ? $title : __('widget:new');
    }
}
