<?php

/* 
 * Mixin for Entity classes that can have Widgets as child entities ($parent->guid == $child->container_guid)
 * Such classes include:
 *  - Organization (child widgets are shown in the site menu) 
 *  - Widget (child widgets could be shown as a sub menu, or as embedded sections depending on type of widget)
 */
class Mixin_WidgetContainer extends Mixin
{  
    function query_widgets()
    {
        return Widget::query_for_entity($this->instance);
    }
                  
    function query_menu_widgets()
    {
        return $this->query_widgets()->where_in_menu();
    }

    function query_published_widgets()
    {
        return $this->query_widgets()->where_published();
    }    
    
    /*
     * Returns a list of widgets that are available to add as children of this container,
     * not including any widgets that are already created
     */
    function get_available_widgets($category)
    {       
        $saved_classes = array();
        
        $saved_widgets = Widget::query_for_entity($this->instance)
            ->columns('guid,container_guid,subtype_id')
            ->filter();
        
        foreach ($saved_widgets as $widget)
        {
            $saved_classes[get_class($widget)] = true;
        }

        $available_widgets = array();
        foreach (Widget::get_default_classes($category) as $cls)
        {
            if (!isset($saved_classes[$cls]))
            {
                $available_widgets[] = $cls::new_for_entity($this->instance);
            }
        }
        
        usort($available_widgets, array('Widget', 'sort'));
        return $available_widgets; 
    }        
    
    public function get_widget_by_name($name)
    {
        return $this->query_widgets()
            ->where('widget_name=?', $name)
            ->show_disabled(true)
            ->get();
    }
    
    function is_section_container()
    {
        return false;
    }
    
    function is_page_container()
    {
        return false;
    }    
    
    function get_edit_url()
    {
        return $this->get_url();
    }
    
    function render_add_child()
    {
        return '';
    }
    
    function new_child_widget_from_input()
    {        
        return null;
    }
    
    function get_default_widget_class_for_name($widget_name)
    {
        return null;
    }
    
    function render_child_view($widget, $args)
    {
        return $widget->render_view($args);
    }
}