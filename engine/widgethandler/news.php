<?php

class WidgetHandler_News extends WidgetHandler
{
    function view($widget)
    {
        return elgg_view("widgets/news_view", array('widget' => $widget));
    }

    function edit($widget)
    {
        return elgg_view("widgets/news_edit", array('widget' => $widget));
    }

    function save($widget)
    {
        // nothing
    }
}

