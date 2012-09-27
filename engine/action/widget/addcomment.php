<?php

class Action_Widget_AddComment extends Action
{
    function before()
    {
        Permission_Public::require_any();
    }

    function process_input()
	{      
        $widget = $this->get_widget();
        
        if (!$widget->guid || !$widget->is_enabled())
        {
            throw new NotFoundException();
        }        
        
		$comments_url = $widget->get_url()."?comments=1";
	
        $user = Session::get_logged_in_user();        
     
        $name = Input::get_string('name');
        $content = Input::get_string('content');
        $location = Input::get_string('location');              
        $email = EmailAddress::validate(Input::get_string('email'));
        
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
		$comment->set_container_entity($widget);
        $comment->email = $email;
		$comment->set_owner_entity($user);
		$comment->name = $name;
        $comment->location = $location;
		$comment->set_content(nl2br(escape($content)), true);
		$comment->save();
	
        $widget->refresh_attributes();
		$widget->save();
        
		if (!$user)
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