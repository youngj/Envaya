<?php

class Controller_Page extends Controller_Profile
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
        $widgetName = $this->request->param('id');        
        $this->widget = $this->org->get_widget_by_name($widgetName); 
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
        $action = new Action_PostComment($this, $this->widget);
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
    
    function action_add_widget()
    {
        $action = new Action_AddWidget($this);
        $action->execute();
    }
}