<?php

class Action_NewPost extends Action
{
    function before()
    {
        $this->require_editor();
    }

    function process_input()
    {        
        $body = get_input('blogbody');
        $org = $this->get_org();
        
        $widget = $org->get_widget_by_class('WidgetHandler_News');
        if (!$widget->is_active())
        {
            $widget->enable();
            $widget->save();
        }

        if (empty($body))
        {
            SessionMessages::add_error(__("blog:blank"));
            redirect_back();
        }
        else
        {
            $uuid = get_input('uuid');

            $duplicate = $org->query_news_updates()
                ->with_metadata('uuid', $uuid)
                ->get();
            
            if (!$duplicate)
            {
                $post = new NewsUpdate();
                $post->owner_guid = Session::get_loggedin_userid();
                $post->container_guid = $org->guid;
                $post->set_content($body);
                $post->uuid = $uuid;
                $post->save();                
                $post->post_feed_items();
                
                SessionMessages::add(__("blog:posted"));
            }
            else
            {
                $post = $duplicate;
            }

            forward($post->get_url());
        }
    }
}