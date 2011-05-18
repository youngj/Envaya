<?php

/*
 * Controller for any Widget on a user's site, accessed by guid. 
 *
 * URL: /<username>/widget/<guid>[/<action>]
 *      /<username>/widget/<container_guid>.<widget_name>/<action> (adding a new widget as a child of another one)
 */
class Controller_Widget extends Controller_User
{   
    static $routes = array(
        array(
            'regex' => '/(?P<container_guid>\d+)\.(?P<widget_name>\w+)(/(?P<action>\w+)\b)?',
            'before' => 'init_widget_from_container',
        ),    
        array(
            'regex' => '/(?P<guid>\d+)(/(?P<action>\w+)\b)?',
            'before' => 'init_widget_from_guid',
        ),
        array(
            'regex' => '/(?P<slug>[\w\-]+)\,(?P<guid>\d+)(/(?P<action>\w+)\b)?',
            'before' => 'init_widget_from_guid',
        ),
    );

    function get_widget()
    {
        return $this->param('widget');
    }
    
    protected function init_widget_from_guid()
    {
        $guid = $this->param('guid');                   
        return $this->init_widget(Widget::get_by_guid($guid, true));
    }
     
    protected function init_widget_from_container()
    {
        $container_guid = $this->param('container_guid');
        $widget_name = $this->param('widget_name');
     
        $container = Widget::get_by_guid($container_guid, true);
        return $this->init_widget($container ? $container->get_widget_by_name($widget_name) : null);
    }

    protected function init_widget($widget)
    {
        if ($widget && $widget->get_root_container_entity()->guid == $this->get_org()->guid)
        {
            $this->params['widget'] = $widget;
        }
        else
        {
            throw new NotFoundException();
        }       
    }
        
    function action_index()
    {
        return $this->index_widget($this->get_widget());
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
        $action = new Action_AddWidget($this, $this->get_widget());
        $action->execute();
    }    

    function action_prev()
    {
        $this->redirect_delta(-1);
    }

    function action_next()
    {
        $this->redirect_delta(1);
    }
        
    function redirect_delta($delta)
    {
        $widget = $this->get_widget();

        $op = ($delta > 0) ? ">" : "<";
        $order = ($delta > 0) ? "asc" : "desc";
        
        $container = $widget->get_container_entity();

        $sibling = $container->query_published_widgets()
            ->where("guid $op ?", $widget->guid)
            ->order_by("guid $order")
            ->get();
        
        if ($sibling)
        {
            return $this->redirect($sibling->get_url());
        }
        
        $sibling = $container->query_published_widgets()
            ->order_by("guid $order")
            ->get();        

        if ($sibling)
        {
            return $this->redirect($sibling->get_url());
        }
    }    
}