<?php

class Action_Discussion_AddMessage extends Action
{
    function process_input()
    {
        $this->validate_security_token();
        
        $topic = $this->get_topic();                       
        $org = $this->get_org();

        $uuid = get_input('uuid');

        $duplicate = $topic->query_messages()
            ->with_metadata('uuid', $uuid)
            ->get();
        if ($duplicate)
        {
            return forward($topic->get_url());
        }               
        
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

        $location = get_input('location');
        
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        
        $user = Session::get_loggedin_user();
        
        $content = Markup::sanitize_html($content, array('Envaya.Untrusted' => !$user));
        
        $time = time();
        
        $message = new DiscussionMessage();
        $message->from_name = $name;
        $message->from_location = $location;
        $message->container_guid = $topic->guid;
        $message->subject = "RE: {$topic->subject}";            
        $message->time_posted = $time;
        $message->set_content($content, true);
        $message->set_metadata('uuid', $uuid);
        
        if ($user)
        {
            $message->from_email = $user->email;
            $message->owner_guid = $user->guid;            
        } 
        $message->save();
        
        if (!$user)
        {
            $message->set_session_owner();
        }
        
        $topic->refresh_attributes();
        $topic->save();    
        
        $message->post_feed_items();
        
        if ($org->is_notification_enabled(Notification::Discussion)
            && (!$user || $user->guid != $org->guid))
        {
            // notify site of message
            $mail = Zend::mail(
                sprintf(__('discussions:notification_subject', $org->language), 
                    $message->from_name, $topic->subject
                )   
            );
            $mail->setBodyHtml(view('emails/discussion_message', array('message' => $message)));
            $org->send_mail($mail);
        }
        
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