<?php

class Action_Widget_DeleteComment extends Action
{
    function before()
    {
        Permission_EditComment::require_for_entity($this->param('comment'));
    }

    function process_input()
	{      
        $comment = $this->param('comment');
        $comment->status = Comment::Deleted;
        $comment->save();

        $widget = $comment->get_container_entity();
        $widget->refresh_attributes();
        $widget->save();

        $email = $comment->email;
        
        if ($email && 
            $widget->query_comments()
                ->where('email = ?', $email)
                ->is_empty())
        {
            EmailSubscription_Comments::delete_for_entity($widget, $email);
        }        
        
        SessionMessages::add(__('comment:deleted'));
        
        $this->redirect($widget->get_url());
	}    
}