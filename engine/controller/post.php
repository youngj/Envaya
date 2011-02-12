<?php

class Controller_Post extends Controller_Profile
{
    protected $post;

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
		$post = $this->post;
		$this->post_comment($post);
    }

    function action_edit()
    {
        $this->require_editor();
        $post = $this->post;

        $title = __('blog:editpost');

        $cancelUrl = get_input('from') ?: $post->get_url();

        PageContext::add_submenu_item(__("canceledit"), $cancelUrl, 'edit');

        $org = $post->get_container_entity();
        $area1 = view("org/editPost", array('entity' => $post));
        $body = view_layout("one_column_padded", view_title($title), $area1);

        $this->page_draw($title,$body);
    }
    
    function action_save()
    {
        $this->require_editor();
        $this->validate_security_token();
        $post = $this->post;
        $org = $this->org;

        $body = get_input('blogbody');

        if (get_input('delete'))
        {
            $org = $post->get_container_entity();
            $post->disable();
            $post->save();
            system_message(__('blog:delete:success'));
            forward($org->get_url()."/news");
        }
        else if (empty($body))
        {
            register_error(__("blog:blank"));
            forward_to_referrer();
        }
        else
        {
            $post->set_content($body, true);
            $post->save();

            system_message(__("blog:updated"));
            forward($post->get_url());
        }
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

            $duplicates = NewsUpdate::query_by_metadata('uuid', $uuid)->where('container_guid=?',$org->guid)->filter();
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

        $op = ($delta > 0) ? ">" : "<";
        $order = ($delta > 0) ? "asc" : "desc";

        $selectWhere = "SELECT * from entities WHERE type='object' AND enabled='yes' AND subtype=? AND container_guid=?";

        $entity = entity_row_to_entity(get_data_row("$selectWhere AND guid $op ? ORDER BY guid $order LIMIT 1",
            array(NewsUpdate::get_subtype_id(), $post->container_guid, $post->guid)
        ));
        if ($entity)
        {
            forward($entity->get_url());
        }

        $entity = entity_row_to_entity(get_data_row("$selectWhere ORDER BY guid $order LIMIT 1",
            array(NewsUpdate::get_subtype_id(), $post->container_guid)
        ));

        if ($entity)
        {
            forward($entity->get_url());
        }
    }
}