<?php

class Action_Discussion_DeleteMessage extends Action
{
    function before()
    {
        Permission_EditDiscussionMessage::require_for_entity($this->param('message'));
    }

    function process_input()
    {       
        $message = $this->param('message');
        $topic = $this->get_topic();
    
        $message->status = DiscussionMessage::Deleted;
        $message->save();
        
        $email = $message->from_email;
        if ($email && 
            $topic->query_messages()
                ->where('status = ?', DiscussionMessage::Published)
                ->where('from_email = ?', $email)
                ->is_empty())
        {
            EmailSubscription_Discussion::delete_for_entity($topic, $email);
        }
        
        SessionMessages::add(__('discussions:message_deleted'));
        
        if ($topic->query_messages()->where('status = ?', DiscussionMessage::Published)->is_empty())
        {
            $topic->delete();
            
            $this->redirect(
                Widget_Discussions::get_or_new_for_entity($this->get_user())->get_url()
            );
        }
        else
        {
            $topic->refresh_attributes();
            $topic->save();                           

            $this->redirect();
        }    
    }
}    