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
    
    function render_items($itemTemplate = 'page_elements/submenu_link_item')
    {
        $items = array();
    
        foreach($this->items as $item)
        {
            $items[] = view($itemTemplate, array(
                'href' => $item->value,
                'label' => $item->name,
                'selected' => $item->selected,
            ));
        }
    
        return $items;
    }
}