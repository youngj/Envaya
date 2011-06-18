<?php

/*
 * Controller for a DiscussionTopic on a user's site, accessed by guid
 *
 * URL: /<username>/topic/<guid>[/<action>] 
 */
class Controller_Topic extends Controller_User
{
    static $routes = array(
        array(
            'regex' => '/(?P<guid>\d+)(/(?P<action>\w+)\b)?',
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
        $topicId = $this->param('guid');
        
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
        
        $topic = $this->get_topic();
        $message = $topic->query_messages()->guid((int)get_input('guid'))->get();
        if (!$message)
        {
            throw new RedirectException();
        }
        
        if (!$message->can_edit())
        {
            throw new RedirectException(__('page:noaccess'));
        }
        
        $message->disable();
        $message->save();
        
        SessionMessages::add(__('discussions:message_deleted'));
        
        if ($topic->query_messages()->is_empty())
        {
            $topic->disable();
            $topic->save();
            
            $this->redirect($this->get_org()->get_widget_by_class('Discussions')->get_url());
        }
        else
        {
            $topic->refresh_attributes();
            $topic->save();                           

            $this->redirect();
        }
    }    
}