<?php

class WidgetHandler_ReportDefinitions extends WidgetHandler
{
    function view($widget)
    {
        return view("widgets/reportdefinitions_view", array('widget' => $widget));
    }
    
    function edit($widget)
    {
        return view("widgets/reportdefinitions_edit", array('widget' => $widget));
    }

    function save($widget)
    {
    }
}
