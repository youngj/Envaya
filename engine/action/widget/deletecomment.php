<?php

class Action_Widget_DeleteComment extends Action
{
    function before()
    {
        $this->require_editor($this->param('comment'));
    }

    function process_input()
	{      
        $comment = $this->param('comment');
        $comment->disable();
        $comment->save();

        $widget = $comment->get_container_entity();
        $widget->refresh_attributes();
        $widget->save();

        SessionMessages::add(__('comment:deleted'));
        
        $this->redirect($widget->get_url());
	}    
}