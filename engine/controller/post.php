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
            $this->not_found();
        }
    }
    
    function action_index()
    {
        $org = $this->org;
        $post = $this->post;
        
        if (!$org->can_view())
        {
            return $this->view_access_denied();
        }        
        
        $this->use_public_layout();

        if ($post->can_edit())
        {
            PageContext::get_submenu('edit')->add_item(__("widget:edit"), "{$post->get_url()}/edit");
        }

        $this->page_draw(array(
            'title' => __('widget:news'),
            'content' => view('news/view_post', array('post' => $post)),
        ));                    
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
        $action = new Action_NewPost($this);
        $action->process_input();
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
            ->where("e.guid $op ?", $post->guid)
            ->order_by("e.guid $order")
            ->limit(1)
            ->get();
        
        if ($newsUpdate)
        {
            forward($newsUpdate->get_url());
        }
        
        $newsUpdate = $org->query_news_updates()
            ->where('status = ?', EntityStatus::Enabled)
            ->order_by("e.guid $order")
            ->limit(1)
            ->get();        

        if ($newsUpdate)
        {
            forward($newsUpdate->get_url());
        }
    }
}