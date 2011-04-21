<?php

class Widget_Invalid extends Widget
{
    private function show_error()
    {
        SessionMessages::add_error(sprintf(__('widget:invalid_class'), $this->subclass));
    }

    function render_view()
    {        
        $this->show_error();
        return '';
    }

    function render_edit()
    {
        $this->show_error();
        return '';
    }    
}