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
        $email = EmailAddress::validate(get_input('email'));
        
        $comments_url = $widget->get_url()."?comments=1";
        
        if (!$content)
        {
			throw new ValidationException(__('comment:empty'));
        }
        
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        Session::set('user_email', $email);
                
        $user = Session::get_loggedin_user();
        
        $content = Markup::sanitize_html($content, array('Envaya.Untrusted' => !$user));                
        
        $prev_email = $comment->email;
        
		$comment->name = $name;
        $comment->location = $location;
        $comment->email = $email;
		$comment->set_content($content, true);
		$comment->save();
	
        $widget->refresh_attributes();
		$widget->save();    	
        
        if ($prev_email && 
            $widget->query_comments()
                ->where('email = ?', $prev_email)
                ->is_empty())
        {
            EmailSubscription_Comments::delete_for_entity($widget, $prev_email);
        }
        
        if ($email)
        {
            EmailSubscription_Comments::init_for_entity($widget, $email);
        }
        
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