<?php

class Action_Discussion_NewTopic extends Action
{            
    function before()
    {
        Permission_Public::require_any();
    }

    function process_input()
	{            
        $user = Session::get_logged_in_user();        
        $site_user = $this->get_user();
        
        $widget = Widget_Discussions::get_or_new_for_entity($site_user);
        if (!$widget->tid)
        {
            Permission_EditUserSite::require_for_entity($widget);
            $widget->save();
        }                
        
        $uniqid = Input::get_string('uniqid');

        $duplicate = Session::get_entity_by_uniqid($uniqid);
        if ($duplicate)
        {
            throw new RedirectException('', $duplicate->get_url());
        }               
        
        $subject = Input::get_string('subject');
        if (!$subject)
        {
            throw new ValidationException(__('discussions:subject_missing'));
        }
        
        $content = Input::get_string('content');
        if (!$content)
        {
            throw new ValidationException(__('discussions:content_missing'));
        }      

        $name = Input::get_string('name');
        if (!$name)
        {
            throw new ValidationException(__('register:user:no_name'));
        }        
        
        if (!$this->check_captcha())
        {
            return $this->render_captcha(array('instructions' => __('discussions:captcha_instructions')));
        }

        $content = Markup::sanitize_html($content, array('Envaya.Untrusted' => !$user));
                
        $location = Input::get_string('location');
        $email = EmailAddress::validate(Input::get_string('email'));        
                
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        Session::set('user_email', $email);
        
        $now = timestamp();
      
        $topic = new DiscussionTopic();
        $topic->subject = $subject;        
        $topic->set_container_entity($site_user);
        $topic->set_owner_entity($user);
        $topic->save();
        
        Session::cache_uniqid($uniqid, $topic);

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
        if (!$widget->is_published())
        {
            Permission_EditUserSite::require_for_entity($widget);
        }

        $this->page_draw(array(
            'title' => __('discussions:title'),
            'content' => view("discussions/topic_new", array('user' => $site_user)),
        ));        
    }
}