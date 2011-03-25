<?php

class WidgetHandler_Discussions extends WidgetHandler
{
    function get_default_title($widget)
    {
        return __('discussions:title');
    }

    function view($widget)
    {
        return view("widgets/discussions_view", array('widget' => $widget));
    }   

    function edit($widget)
    {                
        return view("widgets/discussions_edit", array('widget' => $widget));
    }

    function save($widget)
    {        
        $widget->save();
    }            
}