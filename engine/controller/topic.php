<?php

class Controller_Topic extends Controller_Profile
{
    protected $topic;
    
    function get_topic()
    {
        return $this->topic;
    }

    function before()
    {
        parent::before();

        $topicId = $this->request->param('id');

        if ($topicId == 'new')
        {
            $this->request->action = 'new';
            return;
        }        
        
        $topic = DiscussionTopic::get_by_guid($topicId);
        $org = $this->org;
        if ($topic && $topic->container_guid == $org->guid)
        {
            $this->topic = $topic;
            return;
        }
        else
        {
            $this->use_public_layout();
            $this->not_found();
        }
    }
    
    function action_index()
    {
        $org = $this->org;
        $topic = $this->topic;

        if (!$org->can_view())
        {
            return $this->view_access_denied();
        }        
        
        $this->use_public_layout();

        if ($topic->can_edit())
        {
            PageContext::get_submenu('edit')->add_item(__("widget:edit"), $topic->get_edit_url());
        }

        $this->page_draw(array(
            'title' => __('discussions:title'),
            'content' => view("discussions/topic_view", array('topic' => $topic))
        ));            
    }    
    
    function action_add_message()
    {
        $action = new Action_Discussion_AddMessage($this);
        $action->execute();
    }
    
    function action_invite()
    {
        $action = new Action_Discussion_Invite($this);
        $action->execute();
    }
        
    function action_new()
    {
        $action = new Action_Discussion_NewTopic($this);
        $action->execute();
    }    

    function action_edit()
    {
        $action = new Action_Discussion_Edit($this);
        $action->execute();
    }    
        
    function action_delete_message()
    {
        $this->validate_security_token();        
        
        $topic = $this->topic;
        $message = $topic->query_messages()->where('e.guid = ?', (int)get_input('guid'))->get();
        if (!$message)
        {
            return redirect_back();
        }
        
        if (!$message->can_edit())
        {
            return redirect_back_error(__('noaccess'));
        }
        
        $message->disable();
        $message->save();
        
        SessionMessages::add(__('discussions:message_deleted'));
        
        if ($topic->query_messages()->count() == 0)
        {
            $topic->disable();
            $topic->save();
            
            forward($this->get_org()->get_widget_by_class('WidgetHandler_Discussions')->get_url());
        }
        else
        {
            $topic->refresh_attributes();
            $topic->save();                           

            redirect_back();
        }
    }    
}