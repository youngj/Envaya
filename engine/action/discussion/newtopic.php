<?php

class Action_Discussion_NewTopic extends Action
{            
    function process_input()
	{    
        $this->validate_security_token();
    
        $org = $this->get_org();
        
        $subject = get_input('subject');
        if (!$subject)
        {
            register_error(__('discussions:subject_missing'));
            return $this->render();
        }
        
        $content = get_input('content');
        if (!$content)
        {
            register_error(__('discussions:content_missing'));
            return $this->render();
        }        
        $name = get_input('name');        
        Session::set('user_name', $name);
        
        if (!$this->check_captcha())
        {
            return $this->render_captcha(array('instructions' => __('discussions:captcha_instructions')));
        }
        
        $now = time();
        
        $user = Session::get_loggedin_user();
        
        $topic = new DiscussionTopic();
        $topic->subject = $subject;
        $topic->container_guid = $org->guid;
        if ($user)
        {
            $topic->owner_guid = $user->guid;
        }
        $topic->save();
        
        $message = new DiscussionMessage();
        $message->container_guid = $topic->guid;
        $message->subject = $subject;
        $message->from_name = $name;        
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

        $message->post_feed_items();
        
        system_message(__('discussions:topic_added'));
        
        $widget = $org->get_widget_by_class('WidgetHandler_Discussions');
        
        forward($widget->get_url());    
	}
    
    function render()
    {       
        $this->use_public_layout();        
        $org = $this->get_org();        
        $title = __('discussions:title');        
        $body = $this->org_view_body($title, view("discussions/topic_new", array('org' => $org)));        
        $this->page_draw($title, $body);    
    }
}