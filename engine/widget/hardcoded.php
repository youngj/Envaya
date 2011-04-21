<?php

/* 
 * A widget that renders a hardcoded PHP view (path stored in handler_arg),
 * and also contains free-text HTML content.
 */
class Widget_Hardcoded extends Widget_Generic
{
    function render_view()
    {               
        return view($this->handler_arg, array('widget' => $this));
    }
}

