<?php

class Action_Widget_AddComment extends Action
{
    function process_input()
	{      
        $widget = $this->get_widget();
        
        if (!$widget->guid || !$widget->is_enabled())
        {
            throw new NotFoundException();
        }        
        
		$comments_url = $widget->get_url()."?comments=1";
	
        $userId = Session::get_loggedin_userid();
        
        if ($userId)
        {
            $this->validate_security_token();
        }       
     
        $name = get_input('name');
        $content = get_input('content');
        $location = get_input('location');              
        $email = EmailAddress::validate(get_input('email'));
        
        if (!$content)
        {   
			throw new RedirectException(__('comment:empty'), $comments_url);
        }
        
		if ($widget->query_comments()->where('content = ?', $content)->exists())
		{
			throw new RedirectException(__('comment:duplicate'), $comments_url);
		}
		
        if (!$this->check_captcha())
        {
            $this->render_captcha(array('instructions' => __('comment:captcha_instructions')));
            return true;
        }
		
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        Session::set('user_email', $email);
        
		$comment = new Comment();
		$comment->container_guid = $widget->guid;
        $comment->email = $email;
		$comment->owner_guid = $userId;
		$comment->name = $name;
        $comment->location = $location;
		$comment->set_content(nl2br(escape($content)), true);
		$comment->save();
	
        $widget->refresh_attributes();
		$widget->save();
        
		if (!$userId)
		{
            $comment->set_session_owner();
		}		
        
        $comment->send_notifications(Comment::Added);
        
        if ($email)
        {
            EmailSubscription_Comments::init_for_entity($widget, $email);
        }
		
		SessionMessages::add(__('comment:success'));
		$this->redirect($comments_url);        
	}    
}