<?php
class WidgetHandler_Team extends WidgetHandler_Generic
{
    function edit($widget)
    {
        return view("widgets/team_edit", array('widget' => $widget));
    }

}
