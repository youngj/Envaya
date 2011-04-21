<?php

class Widget_News extends Widget
{
    function render_view()
    {
        $end_guid = (int)get_input('end');    
        return view("widgets/news_view", array('widget' => $this, 'end_guid' => $end_guid));
    }

    function render_edit()
    {
        return view("widgets/news_edit", array('widget' => $this));
    }

    function process_input($action)
    {
        $this->save();
    }
}

