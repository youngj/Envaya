<?php
class WidgetHandler_Updates extends WidgetHandler
{
    function view($widget)
    {
        return elgg_view("widgets/updates_view", array('widget' => $widget));
    }

    function edit($widget)
    {
        return '';
    }

    function save($widget)
    {
    }
}
