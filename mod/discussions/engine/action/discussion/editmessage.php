<?php

class Action_Discussion_EditMessage extends Action
{
    function before()
    {
        Permission_EditDiscussionMessage::require_for_entity($this->param('message'));
    }

    function process_input()
    {
        $message = $this->param('message');
        $topic = $this->get_topic();
    
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

        $location = Input::get_string('location');        
        $email = EmailAddress::validate(Input::get_string('email'));
        
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        Session::set('user_email', $email);
        
        $user = Session::get_logged_in_user();
        
        $content = Markup::sanitize_html($content, array('Envaya.Untrusted' => !$user));
        
        $prev_email = $message->from_email;
        
        $message->from_name = $name;
        $message->from_location = $location;
        $message->from_email = $email;
        $message->set_content($content, true);
        $message->save();
        
        $topic->refresh_attributes();
        $topic->save();
        
        if ($prev_email && 
            $topic->query_messages()
                ->where('from_email = ?', $prev_email)
                ->is_empty())
        {
            EmailSubscription_Discussion::delete_for_entity($topic, $prev_email);
        }
        
        if ($email)
        {
            EmailSubscription_Discussion::init_for_entity($topic, $email);
        }        
        
        SessionMessages::add_html(__('discussions:message_saved')
            . view('discussions/invite_link', array('topic' => $topic)));        
        
        $this->redirect($topic->get_url());    
    }
    
    function render()
    {   
        $this->use_public_layout();        
        $this->page_draw(array(
            'title' => __('discussions:edit_message'),
            'content' => view('discussions/topic_edit_message', array(
                'message' => $this->param('message')
            ))
        ));
    }
}    