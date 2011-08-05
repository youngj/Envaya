<?php

class Action_PostComment extends Action
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
        
		$comment = new Comment();
		$comment->container_guid = $widget->guid;
		$comment->owner_guid = $userId;
		$comment->name = $name;
        $comment->location = $location;
		$comment->content = $content;		
        $comment->queue_guess_language('content');        
		$comment->save();
	
		$widget->num_comments = $widget->query_comments()->count();
		$widget->save();
	
		if (!$userId)
		{
            $comment->set_session_owner();
		}
		
		$org = $widget->get_root_container_entity();
		
		$notification_subject = sprintf(__('comment:notification_subject', $org->language), 
			$comment->get_name());
		$notification_body = view('emails/comment_added', array(
            'comment' => $comment, 
            'url' => "$comments_url#comments",
        ));
		
		if ($org && $org->email && $org->is_notification_enabled(Notification::Comments) 
				&& $userId != $org->guid)
		{		
            $mail = OutgoingMail::create($notification_subject, $notification_body);        
			$mail->send_to_user($org);
		}

        $mail = OutgoingMail::create(
            sprintf(__('comment:notification_admin_subject'), $comment->get_name(), $org->name),
            $notification_body
        );
        $mail->send_to_admin();
		
		SessionMessages::add(__('comment:success'));
		$this->redirect($comments_url);        
	}    
}