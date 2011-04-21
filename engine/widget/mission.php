<?php
class Widget_Mission extends Widget_Generic
{
    function process_input($action)
    {    
        $mission = get_input('content');
        if (!$mission)
        {
            throw new ValidationException(__("setup:mission:blank"));
        }    
        parent::process_input($action);
    }
}
