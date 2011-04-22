<?php

/* 
 * A fallback widget that is used when a widget subclass is not defined
 * (indicating a likely programming or schema migration error), and displays an error message.
 */
class Widget_Invalid extends Widget_Generic
{
    private function show_error()
    {
        SessionMessages::add_error(sprintf(__('widget:invalid_class'), $this->subclass));
    }

    function render_view($args = null)
    {        
        $this->show_error();
        return parent::render_view($args);
    }

    function render_edit()
    {
        $this->show_error();
        return parent::render_edit();
    }    
}