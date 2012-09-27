<?php

class Action_Discussion_AddMessage extends Action
{
    function before()
    {
        Permission_Public::require_any();
    }

    function process_input()
    {
        $topic = $this->get_topic();                       

        $uniqid = Input::get_string('uniqid');

        $duplicate = Session::get_entity_by_uniqid($uniqid);
        if ($duplicate)
        {
            throw new RedirectException('', $duplicate->get_url());
        }               
        
        $name = Input::get_string('name');
        if (!$name)
        {
            throw new ValidationException(__('register:user:no_name'));
        }

        $content = Input::get_string('content');
        if (!$content)
        {
            throw new ValidationException(__('discussions:content_missing'));
        }
        
        if (!$this->check_captcha())
        {
            return $this->render_captcha(array('instructions' => __('discussions:captcha_instructions')));
        }    

        $location = Input::get_string('location');
        $email = EmailAddress::validate(Input::get_string('email'));
        
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        Session::set('user_email', $email);
        
        $user = Session::get_logged_in_user();
        
        $content = Markup::sanitize_html($content, array('Envaya.Untrusted' => !$user));
        
        $time = timestamp();
        
        $message = $topic->new_message();
        $message->from_name = $name;
        $message->from_location = $location;
        $message->from_email = $email;
        $message->subject = "RE: {$topic->subject}";
        $message->time_posted = $time;
        $message->set_content($content, true);
        
        if ($user)
        {
            $message->owner_guid = $user->guid;            
        } 
        $message->save();
        
        Session::cache_uniqid($uniqid, $message);
        
        if (!$user)
        {
            $message->set_session_owner();
        }
        
        $topic->refresh_attributes();
        $topic->save();    
        
        $message->post_feed_items();
        
        $message->send_notifications(DiscussionMessage::Added);
        
        if ($email)
        {
            EmailSubscription_Discussion::init_for_entity($topic, $email);
        }
        
        SessionMessages::add_html(__('discussions:message_added')
            . view('discussions/invite_link', array('topic' => $topic)));        
        
        $this->redirect($topic->get_url());    
    }
    
    function render()
    {   
        $topic = $this->get_topic();
        $reply_to_guid = Input::get_string('reply_to');
        if ($reply_to_guid)
        {
            $reply_to = $topic->query_messages()->guid($reply_to_guid)->get();
        }
        else
        {
            $reply_to = null;
        }
    
        $this->index_topic(array('show_add_message' => true, 'reply_to' => $reply_to));    
    }
}    