<?php
class WidgetHandler_Mission extends WidgetHandler_Generic
{
    function save($widget)
    {    
        $mission = get_input('content');
        if (!$mission)
        {
            throw new ValidationException(__("setup:mission:blank"));
        }    
        parent::save($widget);
    }
}
