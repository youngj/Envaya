<?php

class Action_NewPost extends Action
{
    function before()
    {
        $this->require_editor();
    }

    function process_input()
    {        
        $this->validate_security_token();

        $body = get_input('blogbody');
        $org = $this->get_org();

        if (empty($body))
        {
            register_error(__("blog:blank"));
            forward_to_referrer();
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
                
                system_message(__("blog:posted"));
            }
            else
            {
                $post = $duplicate;
            }

            forward($post->get_url());
        }
    }
}