<?php

class WidgetHandler_Hardcoded extends WidgetHandler_Generic
{
    function __construct($view_name)
    {
        $this->view_name = $view_name;
    }

    function view($widget)
    {               
        return elgg_view($this->view_name, array('widget' => $widget));
    }
}

