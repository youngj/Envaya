<?php

/*
 * Controller for any Widget on a user's site, accessed by guid. 
 *
 * URL: /<username>/widget/<guid>[/<action>]
 *      /<username>/widget/<container_guid>_<widget_name>/<action> (adding a new widget as a child of another one)
 */
class Controller_Widget extends Controller_User
{   
    protected $widget;
    
    function get_widget()
    {
        return $this->widget;
    }
    
    function before()
    {
        parent::before();        
        $this->init_widget();
    }

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
        
    function action_index()
    {
        return $this->index_widget($this->widget);
    }
    
    function action_edit()
    {
        $action = new Action_EditWidget($this);
        $action->execute();
    }
           
	function action_post_comment()
	{
        $action = new Action_PostComment($this);
        $action->execute();
	}           
           
    function action_options()
    {
        $action = new Action_WidgetOptions($this);
        $action->execute();                           
    }           
    
    function action_reorder()
    {
        $action = new Action_ReorderWidget($this);
        $action->execute();
    }
    
    function action_add()
    {
        $action = new Action_AddWidget($this, $this->widget);
        $action->execute();
    }    
}