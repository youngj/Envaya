<?php

class Controller_Topic extends Controller_Profile
{
    protected $topic;

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
    
    private function save_add_message()
    {
        $this->validate_security_token();
                   
        $topic = $this->topic;                       
                   
        $name = get_input('name');
        if (!$name)
        {
            return register_error(__('discussions:name_missing'));
        }

        $content = get_input('content');
        if (!$content)
        {
            return register_error(__('discussions:content_missing'));
        }
        
        Session::set('user_name', $name);
        
        $user = Session::get_loggedin_user();
        
        $time = time();
        
        $message = new DiscussionMessage();
        $message->from_name = $name;
        $message->from_email = $user->email;
        $message->owner_guid = $user->guid;            
        $message->container_guid = $topic->guid;
        $message->subject = "RE: {$topic->subject}";            
        $message->time_posted = $time;
        $message->set_content($content, true);
        $message->save();
        
        $topic->refresh_attributes();
        $topic->save();    
        
        system_message(__('discussions:message_added'));
        
        forward($topic->get_url());    
    }
    
    function use_public_layout()
    {
        $show_menu = get_viewtype() != 'mobile';        
        return parent::use_public_layout($show_menu);    
    }
    
    function action_add_message()
    {
        $this->require_login();
            
        if (Request::is_post())
        {
            $this->save_add_message();
        }
        
        $topic = $this->topic;    

        $this->use_public_layout();
        
        $title = __('discussions:title');
        
        $body = $this->org_view_body($title, view("discussions/topic_add_message", array('topic' => $topic)));
        
        $this->page_draw($title, $body);
    }
    
    private function save_new_topic()
    {
        $org = $this->org;        
        $user = Session::get_loggedin_user();
        
        $subject = get_input('subject');
        if (!$subject)
        {
            return action_error(__('discussions:subject_missing'));
        }
        
        $content = get_input('content');
        if (!$content)
        {
            return action_error(__('discussions:content_missing'));
        }
        
        $name = get_input('name');
        
        Session::set('user_name', $name);
        
        $now = time();
        
        $topic = new DiscussionTopic();
        $topic->subject = $subject;
        $topic->container_guid = $org->guid;
        $topic->owner_guid = $user->guid;
        $topic->save();
        
        $message = new DiscussionMessage();
        $message->container_guid = $topic->guid;
        $message->owner_guid = $user->guid;
        $message->subject = $subject;
        $message->from_name = $name;
        $message->from_email = $user->email;
        $message->time_posted = $now;
        $message->set_content($content, true);
        $message->save();
        
        $topic->refresh_attributes();
        $topic->save();            
        
        system_message(__('discussions:topic_added'));
        
        $widget = $org->get_widget_by_class('WidgetHandler_Discussions');
        
        forward($widget->get_url());
    }    
    
    function action_new()
    {
        $this->require_login();

        if (Request::is_post())
        {
            $this->save_new_topic();
        }
        
        $this->use_public_layout();
        
        $org = $this->org;
        
        $title = __('discussions:title');
        
        $body = $this->org_view_body($title, view("discussions/topic_new", array('org' => $org)));
        
        $this->page_draw($title, $body);
    }    
    
    private function save()
    {
        $this->validate_security_token();        
        $topic = $this->topic;
        $org = $this->org;
        
        if (get_input('delete'))
        {
            $topic->disable();
            $topic->save();
            system_message(__('discussions:topic_deleted'));            
            
            $widget = $org->get_widget_by_class('WidgetHandler_Discussions');
            forward($widget->get_edit_url());
        }

        system_message(__('discussions:topic_saved'));                    
        forward($topic->get_edit_url());
    }
    
    function action_delete_message()
    {
        $this->require_editor();
        $this->validate_security_token();        
        
        $topic = $this->topic;
        $message = $topic->query_messages()->where('e.guid = ?', (int)get_input('guid'))->get();
        if ($message)
        {
            $message->disable();
            $message->save();
            
            $topic->refresh_attributes();
            $topic->save();            

            system_message(__('discussions:message_deleted'));                            
        }
                
        forward($topic->get_edit_url());            
    }
    
    function action_edit()
    {
        $this->require_editor();
        
        if (Request::is_post())
        {
            $this->save();
        }
        
        $topic = $this->topic;
        $this->use_editor_layout();
        
        $title = __('discussions:edit_topic');
        
        $cancelUrl = get_input('from') ?: $topic->get_url();
        PageContext::add_submenu_item(__("canceledit"), $cancelUrl, 'edit');
                
        $body = view_layout('one_column', view_title($title), view("discussions/topic_edit", array('topic' => $topic)));
        
        $this->page_draw($title, $body);        
    }
}