<?php

class Widget_Hardcoded extends Widget_Generic
{
    function render_view()
    {               
        return view($this->handler_arg, array('widget' => $this));
    }
}

