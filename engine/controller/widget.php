<?php

class Controller_Widget extends Controller_Page
{   
    protected function init_widget()
    {
        $guid = $this->request->param('id');   
        
        $id_parts = explode('_', $guid, 2);
        if (sizeof($id_parts) == 2)
        {
            $container_guid = $id_parts[0];
            $widget_name = $id_parts[1];  

            $container = Widget::get_by_guid($container_guid, true);
            $widget = $container ? $container->get_widget_by_name($widget_name) : null;
        }
        else
        {
            $widget = Widget::get_by_guid($guid, true);
        }
        
        if ($widget && $widget->get_root_container_entity()->guid == $this->org->guid)
        {
            $this->widget = $widget;
        }
        else
        {
            $this->not_found();
        }       
    }
}