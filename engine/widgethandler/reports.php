<?php

class WidgetHandler_Reports extends WidgetHandler
{
    function view($widget)
    {
        return view("widgets/reports_view", array('widget' => $widget));
    }
    
    function edit($widget)
    {
        return view("widgets/reports_edit", array('widget' => $widget));
    }

    function save($widget)
    {
    }
}
