<?php
class WidgetHandler_Updates extends WidgetHandler
{
    function view($widget)
    {
        PageContext::set_rss(true);
        return view("widgets/updates_view", array('widget' => $widget));
    }

    function edit($widget)
    {
        return view("widgets/updates_edit", array('widget' => $widget));
    }

    function save($widget)
    {
        $widget->save();
    }
    
    function view_feed($widget)
    {
        return '';
    }
}
