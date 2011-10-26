<?php

class Action_Discussion_EditMessage extends Action
{
    function before()
    {
        $this->require_editor($this->param('message'));
    }

    function process_input()
    {
        $message = $this->param('message');
        $topic = $this->get_topic();
    
        $name = get_input('name');
        if (!$name)
        {
            throw new ValidationException(__('register:user:no_name'));
        }

        $content = get_input('content');
        if (!$content)
        {
            throw new ValidationException(__('discussions:content_missing'));
        }        

        $location = get_input('location');
        
        Session::set('user_name', $name);
        Session::set('user_location', $location);
        
        $user = Session::get_loggedin_user();
        
        $content = Markup::sanitize_html($content, array('Envaya.Untrusted' => !$user));
        
        $message->from_name = $name;
        $message->from_location = $location;
        $message->set_content($content, true);
        $message->save();
        
        $topic->refresh_attributes();
        $topic->save();
        
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