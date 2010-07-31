<?php
class WidgetHandler_Team extends WidgetHandler_Generic
{
    function edit($widget)
    {
        return elgg_view("widgets/team_edit", array('widget' => $widget));
    }

}
