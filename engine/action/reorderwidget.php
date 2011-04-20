<?php

class Action_ReorderWidget extends Action
{
    function before()
    {
        $this->require_editor();
        $this->require_org();
    }
        
    function process_input()
    {        
        $request = $this->get_request();
        $request->headers['Content-Type'] = 'text/javascript';
    
        $widget = $this->get_widget();        
        $delta = ((int)get_input('delta') > 0) ? 1 : -1;
        
        $container = $widget->get_container_entity();        
        $siblings = $container->query_menu_widgets()->filter();                
        
        $adjacent_widget = $this->get_adjacent_widget($siblings, $widget, $delta);
                
        if ($adjacent_widget)
        {
            $min_order = min($widget->menu_order, $adjacent_widget->menu_order);
            $max_order = max($widget->menu_order, $adjacent_widget->menu_order, $min_order + 1);
        
            if ($delta > 0)
            {
                $adjacent_widget->menu_order = $min_order;
                $widget->menu_order = $max_order;
            }
            else
            {
                $widget->menu_order = $min_order;
                $adjacent_widget->menu_order = $max_order;                
            }
            
            $adjacent_widget->save();
            $widget->save();
        }
        
        $request->response = json_encode(array(
            'guids' => array_map(function($w) { return $w->guid; }, $container->query_menu_widgets()->filter())
        ));
    }
    
    function get_adjacent_widget($siblings, $widget, $delta)
    {
        $num_siblings = sizeof($siblings);
        
        for ($i = 0; $i < $num_siblings; $i++)
        {
            $sibling = $siblings[$i];
            $si = $i + $delta;
            
            if ($sibling->guid == $widget->guid)
            {
                if ($si >= 0 && $si < $num_siblings)
                {            
                    return $siblings[$si];                
                }
                else
                {
                    return null;
                }
            }   
        }    
        return null;
    }
}