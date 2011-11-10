<?php

class Submenu
{
    protected $items = array();

    function clear()
    {
        $this->items = array();
    }
    
    function add_item($html)
    {
        $this->items[] = $html;
    }

    function add_link($label, $href, $selected = false) 
    {       
        $this->items[] = view('page_elements/submenu_link_item', array(
            'href' => $href,
            'label' => $label,
            'selected' => $selected,
        ));       
    }
    
    function get_items()
    {
        return $this->items;
    }
}