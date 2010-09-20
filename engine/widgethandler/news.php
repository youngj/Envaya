<?php

class WidgetHandler_News extends WidgetHandler
{
    function view($widget)
    {
        $end_guid = (int)get_input('end');    
        return view("widgets/news_view", array('widget' => $widget, 'end_guid' => $end_guid));
    }

    function edit($widget)
    {
        return view("widgets/news_edit", array('widget' => $widget));
    }

    function save($widget)
    {
        // nothing
    }
}

