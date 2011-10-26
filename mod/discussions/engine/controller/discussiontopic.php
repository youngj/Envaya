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
            'regex' => '/(?P<topic_guid>\d+)/message/(?P<message_guid>\d+)(/(?P<action>\w+)\b)?',
            'action' => 'action_<action>_message',
            'before' => 'init_message_by_guid',
        ),
        array(
            'regex' => '/(?P<topic_guid>\d+)(/(?P<action>\w+)\b)?',
            'defaults' => array('action' => 'index'),
            'before' => 'init_topic_by_guid',
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

    function init_topic_by_guid()
    {
        $topicId = $this->param('topic_guid');
        
        $topic = DiscussionTopic::get_by_guid($topicId);
        $org = $this->get_org();
        if ($topic && $topic->container_guid == $org->guid)
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
        $this->init_topic_by_guid();
        
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
        $org = $this->get_org();
        $topic = $this->get_topic();

        $this->allow_view_types(null);
        $this->allow_content_translation();
        
        if (!$org->can_view())
        {
            return $this->view_access_denied();
        }        
        
        $this->use_public_layout();

        if ($topic->can_edit())
        {
            PageContext::get_submenu('edit')->add_item(__("widget:edit"), $topic->get_edit_url());
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