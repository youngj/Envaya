<?php

class Action_Discussion_NewTopic extends Action
{            
    function process_input()
	{            
        $user = Session::get_loggedin_user();
        $org = $this->get_org();
        
        $widget = $org->get_widget_by_class('WidgetHandler_Discussions');
        if (!$widget->is_active())
        {
            if ($widget->can_edit())
            {        
                $widget->enable();
                $widget->save();
            }
            else
            {                
                return $this->not_found();
            }
        }                
        
        $uuid = get_input('uuid');

        $duplicate = $org->query_discussion_topics()
            ->with_metadata('uuid', $uuid)
            ->get();
        if ($duplicate)
        {
            return forward($duplicate->get_url());
        }               
        
        $subject = get_input('subject');
        if (!$subject)
        {
            SessionMessages::add_error(__('discussions:subject_missing'));
            return $this->render();
        }
        
        $content = get_input('content');
        if (!$content)
        {
            SessionMessages::add_error(__('discussions:content_missing'));
            return $this->render();
        }        
        
        if (!$this->check_captcha())
        {
            return $this->render_captcha(array('instructions' => __('discussions:captcha_instructions')));
        }

        $content = Markup::sanitize_html($content, array('Envaya.Untrusted' => !$user));
        
        $name = get_input('name');        
        Session::set('user_name', $name);
        $location = get_input('location');
        Session::set('user_location', $location);
        
        $now = time();
      
        $topic = new DiscussionTopic();
        $topic->subject = $subject;
        $topic->container_guid = $org->guid;
        if ($user)
        {
            $topic->owner_guid = $user->guid;
        }
        $topic->set_metadata('uuid', $uuid);
        $topic->save();
        
        $message = new DiscussionMessage();
        $message->container_guid = $topic->guid;
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

        if (!$user)
        {
            $message->set_session_owner();
        }
        
        $message->post_feed_items();
        
        if ($org->is_notification_enabled(Notification::Discussion)
            && (!$user || $user->guid != $org->guid))
        {
            // notify site of message
           
            $mail = OutgoingMail::create(
                sprintf(__('discussions:notification_topic_subject', $org->language), 
                    $message->from_name, $topic->subject
                )   
            );
            $mail->setBodyHtml(view('emails/discussion_message', array('message' => $message)));            
            $mail->send_to_user($org);
        }        
        
        SessionMessages::add_html(__('discussions:topic_added')
            . view('discussions/invite_link', array('topic' => $topic)));        

        forward($topic->get_url());    
	}
    
    function render()
    {       
        $this->use_public_layout();        
        $org = $this->get_org();       

        $widget = $org->get_widget_by_class('WidgetHandler_Discussions');
        if (!$widget->is_active() && !$widget->can_edit())
        {
            return $this->not_found();
        }

        $this->page_draw(array(
            'title' => __('discussions:title'),
            'content' => view("discussions/topic_new", array('org' => $org)),
        ));        
    }
}