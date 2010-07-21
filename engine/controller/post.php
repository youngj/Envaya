<?php

class Controller_Post extends Controller_Profile
{
    protected $post;

    function before()
    {
        parent::before();

        $postId = $this->request->param('id');
        $post = get_entity($postId);

        $org = $this->org;
        if ($post && $post->container_guid == $org->guid)
        {
            $this->post = $post;
            return;
        }
        else
        {
            org_page_not_found($org);
        }
    }

    function action_index()
    {
        $org = $this->org;
        $post = $this->post;

        $this->use_public_layout();

        if ($post->canEdit())
        {
            add_submenu_item(elgg_echo("widget:edit"), "{$post->getUrl()}/edit", 'edit');
        }

        $title = elgg_echo('widget:news');

        if (!$org->canView())
        {
            $org->showCantViewMessage();
            $body = '';
        }
        else
        {
            $body = org_view_body($org, $title, elgg_view("org/blogPost", array('entity'=> $post)));
        }

        $this->page_draw($title,$body);
    }

    function action_edit()
    {
        $this->require_editor();
        $post = $this->post;

        $title = elgg_echo('blog:editpost');

        $cancelUrl = get_input('from') ?: $post->getUrl();

        add_submenu_item(elgg_echo("canceledit"), $cancelUrl, 'edit');

        $org = $post->getContainerEntity();
        $area1 = elgg_view("org/editPost", array('entity' => $post));
        $body = elgg_view_layout("one_column_padded", elgg_view_title($title), $area1);

        $this->page_draw($title,$body);
    }

    function action_preview()
    {
        $this->request->headers['Content-type'] = 'text/javascript';
        $this->request->response = json_encode($this->post->jsProperties());
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

        $entity = entity_row_to_elggstar(get_data_row("$selectWhere AND guid $op ? ORDER BY guid $order LIMIT 1",
            array(T_blog, $post->container_guid, $post->guid)
        ));
        if ($entity)
        {
            forward($entity->getURL());
        }

        $entity = entity_row_to_elggstar(get_data_row("$selectWhere ORDER BY guid $order LIMIT 1",
            array(T_blog, $post->container_guid)
        ));

        if ($entity)
        {
            forward($entity->getURL());
        }
    }
}