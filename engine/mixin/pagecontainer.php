<?php

class Mixin_PageContainer extends Mixin_WidgetContainer
{
    function is_page_container()
    {
        return true;
    }            
    
    function new_child_widget_from_input()
    {        
        $widget_name = get_input('widget_name');
        if (!$widget_name || !Widget::is_valid_name($widget_name))
        {
            throw new ValidationException(__('widget:bad_name'));            
        }
        
        $widget = $this->get_widget_by_name($widget_name);
        if ($widget)
        {
            if ((timestamp() - $widget->time_created > 30) || !($widget instanceof Widget_Generic))
            {        
                throw new ValidationException(
                    sprintf(__('widget:duplicate_name'),
                        "<a href='{$widget->get_edit_url()}'><strong>".__('clickhere')."</strong></a>"),
                    true
                );
            }
        }
        else
        {
            $widget = Widget_Generic::new_for_entity($this->instance, array('widget_name' => $widget_name));
        }
        return $widget;
    }
    
    function render_add_child()
    {
        return view("widgets/add_page", array('container' => $this));
    }        
}