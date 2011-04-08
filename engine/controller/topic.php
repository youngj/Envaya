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
        
        $topic = get_entity($topicId);
        $org = $this->org;
        if ($topic && $topic->container_guid == $org->guid && $topic instanceof DiscussionTopic)
        {
            $this->topic = $topic;
            return;
        }
        else
        {
            $this->use_public_layout();
            $this->org_page_not_found();
        }
    }
    
    function action_index()
    {
        $org = $this->org;
        $topic = $this->topic;

        $this->use_public_layout();

        if ($topic->can_edit())
        {
            PageContext::add_submenu_item(__("widget:edit"), $topic->get_edit_url(), 'edit');
        }

        $title = __('discussions:title');

        if (!$org->can_view())
        {
            $this->show_cant_view_message();
            $body = '';
        }
        else
        {                    
            $body = $this->org_view_body($title, view("discussions/topic_view", array('topic' => $topic)));
        }
        
        $this->page_draw($title, $body);
    }    
    
    function use_public_layout($show_menu = true)
    {
        $show_menu = get_viewtype() != 'mobile';        
        return parent::use_public_layout($show_menu);    
    }
    
    function action_add_message()
    {
        $action = new Action_Discussion_AddMessage($this);
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
            return forward_to_referrer();
        }
        
        if (!$message->can_edit())
        {
            return action_error(__('noaccess'));
        }
        
        $message->disable();
        $message->save();
        
        system_message(__('discussions:message_deleted'));
        
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

            forward_to_referrer();
        }
    }    
}