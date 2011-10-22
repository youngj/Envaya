<?php

class Action_Discussion_NewTopic extends Action
{            
    function process_input()
	{            
        $user = Session::get_loggedin_user();
        $org = $this->get_org();
        
        $widget = $org->get_widget_by_class('Discussions');
        if (!$widget->is_enabled())
        {
            if ($widget->can_edit())
            {        
                $widget->enable();
                $widget->save();
            }
            else
            {                
                throw new NotFoundException();
            }
        }                
        
        $uniqid = get_input('uniqid');

        $duplicate = $org->query_discussion_topics()
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
                
        Session::set('user_name', $name);
        $location = get_input('location');
        Session::set('user_location', $location);
        
        $now = timestamp();
      
        $topic = new DiscussionTopic();
        $topic->subject = $subject;        
        $topic->container_guid = $org->guid;
        if ($user)
        {
            $topic->owner_guid = $user->guid;
        }
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
            $message->owner_guid = $user->guid;
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
        
        SessionMessages::add_html(__('discussions:topic_added')
            . view('discussions/invite_link', array('topic' => $topic)));        

        $this->redirect($topic->get_url());    
	}
    
    function render()
    {       
        $this->use_public_layout();        
        $org = $this->get_org();       

        $widget = $org->get_widget_by_class('Discussions');
        if (!$widget->is_enabled() && !$widget->can_edit())
        {
            throw new NotFoundException();
        }

        $this->page_draw(array(
            'title' => __('discussions:title'),
            'content' => view("discussions/topic_new", array('org' => $org)),
        ));        
    }
}