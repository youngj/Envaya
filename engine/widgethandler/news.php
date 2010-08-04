<?php

class WidgetHandler_News extends WidgetHandler
{
    function view($widget)
    {
        return view("widgets/news_view", array('widget' => $widget));
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

