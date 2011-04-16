<?php

class Action_PostComment extends Action
{
    protected $entity;

    function __construct($controller, $entity)
    {
        parent::__construct($controller);
        $this->entity = $entity;
    }	
        
    function process_input()
	{      
        $entity = $this->entity;
        
        if (!$entity->guid || !$entity->is_enabled())
        {
            return $this->not_found();
        }        
        
		$comments_url = $entity->get_url()."?comments=1";
	
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
            SessionMessages::add_error(__('comment:empty'));
			Session::save_input();
			return forward($comments_url);
        }
        
		if ($entity->query_comments()->where('content = ?', $content)->count() > 0)
		{
			SessionMessages::add_error(__('comment:duplicate'));
			Session::save_input();
			return forward($comments_url);
		}
		
        if (!$this->check_captcha())
        {
            $this->render_captcha(array('instructions' => __('comment:captcha_instructions')));
            return true;
        }
		
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        
		$comment = new Comment();
		$comment->container_guid = $entity->guid;
		$comment->owner_guid = $userId;
		$comment->name = $name;
        $comment->location = $location;
		$comment->content = $content;
		$comment->language = GoogleTranslate::guess_language($content);
		$comment->save();
	
		$entity->num_comments = $entity->query_comments()->count();
		$entity->save();
	
		if (!$userId)
		{
            $comment->set_session_owner();
		}
		
		$org = $entity->get_root_container_entity();
		
		$notification_subject = sprintf(__('comment:notification_subject', $org->language), 
			$comment->get_name());
		$notification_body = sprintf(__('comment:notification_body', $org->language),
			$comment->content,
			"$comments_url#comments"
		);
		
		if ($org && $org->email && $org->is_notification_enabled(Notification::Comments) 
				&& $userId != $org->guid)
		{		
            $mail = Zend::mail($notification_subject, $notification_body);        
			$org->send_mail($mail);
		}

        $mail = Zend::mail(
            sprintf(__('comment:notification_admin_subject'), $comment->get_name(), $org->name),
            $notification_body
        );

		send_admin_mail($mail);
		
		SessionMessages::add(__('comment:success'));
		forward($comments_url);
        return true;
	}    
}