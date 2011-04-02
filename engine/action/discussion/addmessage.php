<?php

class Action_Discussion_AddMessage extends Action
{
    function process_input()
    {
        $this->validate_security_token();
        
        $topic = $this->get_topic();                       
                   
        $name = get_input('name');
        if (!$name)
        {
            register_error(__('discussions:name_missing'));
            return $this->render();
        }

        $content = get_input('content');
        if (!$content)
        {
            register_error(__('discussions:content_missing'));
            return $this->render();
        }
        
        if (!$this->check_captcha())
        {
            return $this->render_captcha(array('instructions' => __('discussions:captcha_instructions')));
        }        
        
        Session::set('user_name', $name);
        
        $user = Session::get_loggedin_user();
        
        $time = time();
        
        $message = new DiscussionMessage();
        $message->from_name = $name;
        $message->container_guid = $topic->guid;
        $message->subject = "RE: {$topic->subject}";            
        $message->time_posted = $time;
        $message->set_content($content, true);
        
        if ($user)
        {
            $message->from_email = $user->email;
            $message->owner_guid = $user->guid;            
        }        
        $message->save();
                
        $topic->refresh_attributes();
        $topic->save();    
        
        $message->post_feed_items();        
        
        system_message(__('discussions:message_added'));
        
        forward($topic->get_url());    
    }
    
    function render()
    {    
        $topic = $this->get_topic();    
        
        $this->use_public_layout();                
        $title = __('discussions:title');                
        $body = $this->org_view_body($title, view("discussions/topic_add_message", array('topic' => $topic)));                
        $this->page_draw($title, $body);    
    }
}    