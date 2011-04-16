<?php

class WidgetHandler_Invalid extends WidgetHandler
{
    function show_error($widget)
    {
        SessionMessages::add_error(sprintf(__('widget:invalid_class'), $widget->handler_class));
    }

    function view($widget)
    {        
        $this->show_error($widget);
        return '';
    }

    function edit($widget)
    {
        $this->show_error($widget);
        return '';
    }

    function save($widget)
    {
    }
}

