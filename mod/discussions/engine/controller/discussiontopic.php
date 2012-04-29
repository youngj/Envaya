<?php

/*
 * Controller for a DiscussionTopic on a user's site, accessed by guid
 *
 * URL: /<username>/topic/<topic_guid>[/<action>] 
 */
class Controller_DiscussionTopic extends Controller_User
{
    static $routes = array(
        array(
            'regex' => '/(?P<topic_tid>\d+)/message/(?P<message_guid>\w+)(/(?P<action>\w+)\b)?',
            'action' => 'action_<action>_message',
            'before' => 'init_message_by_guid',
        ),
        array(
            'regex' => '/(?P<topic_tid>\d+)(/(?P<action>\w+)\b)?',
            'defaults' => array('action' => 'index'),
            'before' => 'init_topic_by_tid',
        ),
        array(
            'regex' => '/(?P<action>new)\b',
        ),        
    );

    protected $topic;
    
    function get_topic()
    {
        return $this->param('topic');
    }

    function init_topic_by_tid()
    {
        $topicId = $this->param('topic_tid');
        
        $topic = DiscussionTopic::query()->where('tid = ?', $topicId)->get();
        $user = $this->get_user();
        if ($topic && $topic->container_guid === $user->guid)
        {
            $this->params['topic'] = $topic;
        }
        else
        {
            $this->use_public_layout();
            throw new NotFoundException();
        }
    }
    
    function init_message_by_guid()
    {
        $this->init_topic_by_tid();
        
        $message_guid = $this->param('message_guid');        
        $message = $this->get_topic()->query_messages()->guid($message_guid)->get();
        
        if ($message)
        {
            $this->params['message'] = $message;
        }
        else
        {
            $this->use_public_layout();
            throw new NotFoundException();
        }
    }
    
    
    function action_index()
    {
        $this->index_topic();
    }    

    function index_topic($vars = null)
    {
        $user = $this->get_user();
        $topic = $this->get_topic();

        $this->allow_view_types(null);
        $this->allow_content_translation();
        
        Permission_ViewUserSite::require_for_entity($user);
        
        $this->use_public_layout();

        if (Permission_EditUserSite::has_for_entity($topic))
        {
            PageContext::get_submenu('top')->add_link(__("widget:edit"), $topic->get_edit_url());
        }

        if (!$vars)
        {
            $vars = array();
        }
        $vars['topic'] = $topic;
        
        $this->page_draw(array(
            'title' =>  __('discussions:title'),
            'content' => view("discussions/topic_view", $vars)
        ));            
    }
    
    function action_add_message()
    {
        $action = new Action_Discussion_AddMessage($this);
        $action->execute();
    }
            
    function action_edit_message()
    {
        $action = new Action_Discussion_EditMessage($this);
        $action->execute();
    }
            
    function action_delete_message()
    {
        $action = new Action_Discussion_DeleteMessage($this);
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
}