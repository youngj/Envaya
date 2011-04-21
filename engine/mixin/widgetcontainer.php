<?php

/* 
 * Mixin for Entity classes that can have Widgets as child entities ($parent->guid == $child->container_guid)
 * Such classes include:
 *  - Organization (child widgets are shown in the site menu) 
 *  - Widget (child widgets could be shown as a sub menu, or as embedded sections depending on type of widget)
 */
class Mixin_WidgetContainer extends Mixin
{        
    public function query_widgets()
    {
        return Widget::query()
            ->where('container_guid=?', $this->guid)
            ->order_by('menu_order');
    }
    
    public function query_widgets_by_class($subclass)
    {
        return $this->query_widgets()->where('subclass = ?', $subclass);
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
        
    public function new_widget_by_name($widget_name, $default_subclass = 'Generic')
    {
        $props = @Widget::$default_widgets[$widget_name] ?: array();
        
        $subclass = (@$props['subclass'] ?: $default_subclass);
        
        $cls = "Widget_{$subclass}";
        
        $widget = new $cls();
        $widget->widget_name = $widget_name;    
        $widget->container_guid = $this->guid;        
        $widget->menu_order = @$props['menu_order'] ?: 1000;
        $widget->subclass = $subclass;

        return $widget;
    }            
    
    public function new_widget_by_class($subclass)
    {
        $default_names = Widget::get_default_names_by_class($subclass);
        if (sizeof($default_names))
        {
            return $this->new_widget_by_name($default_names[0]);
        }
        else
        {
            return $this->new_widget_by_name(uniqid("",true), $subclass);
        }
    }
        
    public function get_widget_by_class($subclass)
    {
        return $this->query_widgets()
            ->where('subclass = ?', $subclass)
            ->show_disabled(true)
            ->order_by('status desc') // prefer enabled over disabled widgets
            ->get()
        ?: $this->new_widget_by_class($subclass);        
    }
    
    public function get_widget_by_name($name, $default_subclass = 'Generic')
    {
        return $this->query_widgets()
            ->where('widget_name=?', $name)
            ->show_disabled(true)
            ->get()
            ?: $this->new_widget_by_name($name, $default_subclass);
    }        
    
    function get_widget_dates()
    {   
        $sql = "SELECT guid, time_created from widgets WHERE status=1 AND container_guid=? ORDER BY guid ASC";
        return Database::get_rows($sql, array($this->guid));
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
    
    function render_add_child_title()
    {
        return $this->is_section_container() ? sprintf(__("widget:add_section"), $this->get_title()) : __('widget:add');
    }
    
    function new_child_widget_from_input()
    {        
        return null;
    }
}