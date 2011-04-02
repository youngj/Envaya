<?php

class Controller_Post extends Controller_Profile
{
    protected $post;

    function get_post()
    {
        return $this->post;
    }
    
    function before()
    {
        parent::before();

        $postId = $this->request->param('id');

        if ($postId == 'new')
        {
            $this->request->action = 'new';
            return;
        }

        $post = get_entity($postId);
        $org = $this->org;
        if ($post && $post->container_guid == $org->guid && $post instanceof NewsUpdate)
        {
            $this->post = $post;
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
        $post = $this->post;

        $show_menu = get_viewtype() != 'mobile';
        
        $this->use_public_layout($show_menu);

        if ($post->can_edit())
        {
            PageContext::add_submenu_item(__("widget:edit"), "{$post->get_url()}/edit", 'edit');
        }

        $title = __('widget:news');

        if (!$org->can_view())
        {
            $this->show_cant_view_message();
            $body = '';
        }
        else
        {                    
            $body = $this->org_view_body($title, view("org/blogPost", array('entity'=> $post)));
        }

        $this->page_draw($title,$body);
    }
    
    function action_post_comment()
    {   
		$action = new Action_PostComment($this, $this->post);
        $action->process_input();
    }

    function action_edit()
    {
        $action = new Action_EditPost($this);
        $action->execute();
    }
    
    function action_new()
    {
        $this->require_editor();
        $this->validate_security_token();

        $body = get_input('blogbody');
        $org = $this->org;

        if (empty($body))
        {
            register_error(__("blog:blank"));
            forward_to_referrer();
        }
        else
        {
            $uuid = get_input('uuid');

            $duplicates = NewsUpdate::query()->with_metadata('uuid', $uuid)->where('container_guid=?',$org->guid)->filter();
            if (!sizeof($duplicates))
            {
                $post = new NewsUpdate();
                $post->owner_guid = Session::get_loggedin_userid();
                $post->container_guid = $org->guid;
                $post->set_content($body, true);
                $post->uuid = $uuid;
                $post->save();                
                $post->post_feed_items();
                
                system_message(__("blog:posted"));
            }
            else
            {
                $post = $duplicates[0];
            }

            forward($post->get_url());
        }
    }

    function action_preview()
    {
        $this->request->headers['Content-type'] = 'text/javascript';
        $this->request->response = json_encode($this->post->js_properties());
    }

    function action_prev()
    {
        $this->redirect_delta(-1);
    }

    function action_next()
    {
        $this->redirect_delta(1);
    }

    function redirect_delta($delta)
    {
        $post = $this->post;
        
        $org = $this->org;

        $op = ($delta > 0) ? ">" : "<";
        $order = ($delta > 0) ? "asc" : "desc";

        $newsUpdate = $org->query_news_updates()
            ->where('status = ?', EntityStatus::Enabled)
            ->where("guid $op ?", $post->guid)
            ->order_by("guid $order")
            ->limit(1)
            ->get();
        
        if ($newsUpdate)
        {
            forward($newsUpdate->get_url());
        }
        
        $newsUpdate = $org->query_news_updates()
            ->where('status = ?', EntityStatus::Enabled)
            ->order_by("guid $order")
            ->limit(1)
            ->get();        

        if ($entity)
        {
            forward($entity->get_url());
        }
    }
}