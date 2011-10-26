<?php

class Action_Widget_EditComment extends Action
{
    function before()
    {
        $this->require_editor($this->param('comment'));
    }    

    function process_input()
	{      		
        $widget = $this->get_widget();
		$comment = $this->param('comment');        
    
        $name = get_input('name');
        $content = get_input('content');
        $location = get_input('location');
        
        $comments_url = $widget->get_url()."?comments=1";
        
        if (!$content)
        {
			throw new ValidationException(__('comment:empty'));
        }
        
        Session::set('user_name', $name);
        Session::set('user_location', $location);
                
        $user = Session::get_loggedin_user();
        
        $content = Markup::sanitize_html($content, array('Envaya.Untrusted' => !$user));                
        
		$comment->name = $name;
        $comment->location = $location;
		$comment->set_content($content, true);
		$comment->save();
	
        $widget->refresh_attributes();
		$widget->save();    	
        
		SessionMessages::add(__('comment:saved'));
		$this->redirect($comments_url);        
	}    
    
    function render()
    {
        $this->use_public_layout();
        
        $this->page_draw(array(
            'title' => __('comment:edit'),
            'content' => view('widgets/comment_edit', array(
                'comment' => $this->param('comment')
            ))
        ));
    }
}