<?php

/* 
 * Mixin for Entity classes that can have Widgets as child entities ($parent->guid == $child->container_guid)
 * Such classes include:
 *  - Organization (child widgets are shown in the site menu) 
 *  - Widget (child widgets could be shown as a sub menu, or as embedded sections depending on WidgetHandler)
 */
class Mixin_WidgetContainer extends Mixin
{        
    public function query_widgets()
    {
        return Widget::query()
            ->where('container_guid=?', $this->guid)
            ->order_by('menu_order');
    }
    
    public function query_menu_widgets()
    {
        return $this->query_widgets()
            ->where('in_menu = 1')
            ->where('status = ?', EntityStatus::Enabled);
    }
    
    /*
     * Returns a list of widgets that are available to add as children of this container,
     * not including any widgets that are already created
     */
    public function get_available_widgets($mode)
    {       
        $savedWidgetsMap = array();                
        foreach ($this->query_widgets()->filter() as $widget)
        {
            $savedWidgetsMap[$widget->widget_name] = $widget;
        }

        $availableWidgets = array();
        foreach (Widget::get_default_names() as $name)
        {
            if (!isset($savedWidgetsMap[$name]) && @Widget::$default_widgets[$name][$mode])
            {
                $availableWidgets[] = $this->new_widget_by_name($name);
            }            
        }        
        usort($availableWidgets, array('Widget', 'sort'));
        return $availableWidgets; 
    }        
        
    public function new_widget_by_name($widget_name)
    {
        $props = @Widget::$default_widgets[$widget_name] ?: array();
        
        $widget = new Widget();
        $widget->widget_name = $widget_name;    
        $widget->container_guid = $this->guid;        
        $widget->menu_order = @$props['menu_order'] ?: 1000;
        $widget->handler_class = @$props['handler_class'] ?: 'WidgetHandler_Generic';            

        return $widget;
    }            
        
    public function get_widget_by_class($class_name)
    {
        $widget = $this->query_widgets()
            ->where('handler_class = ?', $class_name)
            ->show_disabled(true)->get();
        
        if (!$widget)
        {
            $default_names = Widget::get_default_names_by_class($class_name);
            if (sizeof($default_names))
            {
                $widget = $this->new_widget_by_name($default_names[0]);
            }
        }
        
        return $widget;
    }
    
    public function get_widget_by_name($name)
    {
        return $this->query_widgets()->where('widget_name=?', $name)->show_disabled(true)->get()
            ?: $this->new_widget_by_name($name);
    }        
}
