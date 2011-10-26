<?php

/*
 * Controller for any Widget on a user's site, accessed by guid. 
 *
 * Expects the parent controller (Controller_UserSite) to supply a Widget instance
 * as a parameter named 'widget'.
 *
 * URL: /<username>/widget/<guid>[/<action>]
 *      /<username>/widget/<container_guid>.<widget_name>/<action> (adding a new widget as a child of another one)
 */
class Controller_Widget extends Controller_User
{   
    static $routes = array(
        array(
            'regex' => '/comment/(?P<comment_guid>\d+)(/(?P<action>\w+)\b)?',
            'action' => 'action_<action>_comment',
            'before' => 'init_comment_by_guid',
        ),    
        array(
            'regex' => '(/(?P<action>\w+)\b)?',
            'defaults' => array('action' => 'index'),
        ),    
    );

    function get_widget()
    {
        return $this->param('widget');
    }    
        
    function init_comment_by_guid()
    {
        $comment_guid = $this->param('comment_guid');
        
        $comment = $this->get_widget()
            ->query_comments()
            ->guid($comment_guid)
            ->get();            
        if ($comment)
        {
            $this->params['comment'] = $comment;
        }
        else
        {
            $this->use_public_layout();
            throw new NotFoundException();
        }
    }
        
    function action_index()
    {
        return $this->index_widget($this->get_widget());
    }
    
    function action_edit()
    {
        $action = new Action_Widget_Edit($this);
        $action->execute();
    }
           
	function action_add_comment()
	{
        $action = new Action_Widget_AddComment($this);
        $action->execute();
	}    

	function action_edit_comment()
	{
        $action = new Action_Widget_EditComment($this);
        $action->execute();
	}
    
    function action_delete_comment()
    {
        $action = new Action_Widget_DeleteComment($this);
        $action->execute();    
    }
           
    function action_options()
    {
        $action = new Action_Widget_Options($this);
        $action->execute();                           
    }           
    
    function action_reorder()
    {
        $action = new Action_Widget_Reorder($this);
        $action->execute();
    }
    
    function action_add()
    {
        $action = new Action_Widget_Add($this, $this->get_widget());
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