<?php

class Submenu
{
    protected $items = array();

    function clear()
    {
        $this->items = array();
    }

    function add_item($label, $link, $selected = false) 
    {
        $item = new stdClass;
        $item->value = $link;
        $item->name = $label;
        $item->selected = $selected;
        
        $this->items[] = $item;
    }
    
    function render($itemTemplate = 'canvas_header/link_submenu', $groupTemplate = 'canvas_header/basic_submenu_group')
    {
        $submenu = array();
    
        foreach($this->items as $item)
        {
            $submenu[] = view($itemTemplate, array(
                'href' => $item->value,
                'label' => $item->name,
                'selected' => $item->selected,
            ));
        }

        return view($groupTemplate, array(
            'submenu' => $submenu,
        ));
    }        
}