<?php

class Widget_Discussions extends Widget
{
    function get_default_title()
    {
        return __('discussions:title');
    }

    function render_view()
    {
        return view("widgets/discussions_view", array('widget' => $this));
    }   

    function render_edit()
    {                
        return view("widgets/discussions_edit", array('widget' => $this));
    }

    function process_input($action)
    {        
        $this->save();
    }            
}