<?php

class Action_Discussion_NewTopic extends Action
{            
    function process_input()
	{            
        $user = Session::get_logged_in_user();        
        $site_user = $this->get_user();
        
        $widget = Widget_Discussions::get_or_new_for_entity($site_user);
        if (!$widget->is_enabled())
        {
            Permission_EditUserSite::require_for_entity($widget);            
            $widget->enable();
            $widget->save();
        }                
        
        $uniqid = get_input('uniqid');

        $duplicate = DiscussionTopic::query_for_user($site_user)
            ->with_metadata('uniqid', $uniqid)
            ->get();
        if ($duplicate)
        {
            throw new RedirectException('', $duplicate->get_url());
        }               
        
        $subject = get_input('subject');
        if (!$subject)
        {
            throw new ValidationException(__('discussions:subject_missing'));
        }
        
        $content = get_input('content');
        if (!$content)
        {
            throw new ValidationException(__('discussions:content_missing'));
        }      

        $name = get_input('name');
        if (!$name)
        {
            throw new ValidationException(__('register:user:no_name'));
        }        
        
        if (!$this->check_captcha())
        {
            return $this->render_captcha(array('instructions' => __('discussions:captcha_instructions')));
        }

        $content = Markup::sanitize_html($content, array('Envaya.Untrusted' => !$user));
                
        $location = get_input('location');
        $email = EmailAddress::validate(get_input('email'));        
                
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        Session::set('user_email', $email);
        
        $now = timestamp();
      
        $topic = new DiscussionTopic();
        $topic->subject = $subject;        
        $topic->set_container_entity($site_user);
        $topic->set_owner_entity($user);
        $topic->set_metadata('uniqid', $uniqid);
        $topic->save();

        $message = $topic->new_message();        
        $message->subject = $subject;
        $message->from_name = $name;        
        $message->from_location = $location;
        $message->time_posted = $now;
        $message->set_content($content, true);
                
        if ($user)
        {
            $message->set_owner_entity($user);
            $message->from_email = $user->email;
        }                
        $message->save();        

        $topic->first_message_guid = $message->guid;
        $topic->refresh_attributes();
        $topic->save(); 
        $topic->queue_guess_language('subject');

        if (!$user)
        {
            $message->set_session_owner();
        }
        
        $message->post_feed_items();
        
        $message->send_notifications(DiscussionMessage::NewTopic);
        
        if ($email)
        {
            EmailSubscription_Discussion::init_for_entity($topic, $email);
        }        
        
        SessionMessages::add_html(__('discussions:topic_added')
            . view('discussions/invite_link', array('topic' => $topic)));        

        $this->redirect($topic->get_url());    
	}
    
    function render()
    {       
        $this->use_public_layout();        
        $site_user = $this->get_user();       

        $widget = Widget_Discussions::get_or_new_for_entity($site_user);
        if (!$widget->is_enabled())
        {
            Permission_EditUserSite::require_for_entity($widget);
        }

        $this->page_draw(array(
            'title' => __('discussions:title'),
            'content' => view("discussions/topic_new", array('user' => $site_user)),
        ));        
    }
}