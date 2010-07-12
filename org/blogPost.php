<?php

    $postId = (int) get_input('blogpost');

    if ($post = get_entity($postId))
    {
        $canedit = $post->canEdit();
        if ($canedit)
        {
            add_submenu_item(elgg_echo("widget:edit"), "{$post->getUrl()}/edit", 'edit');
        }

        $org = $post->getContainerEntity();
        set_theme($org->theme);

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

        page_draw($title,$body);
    }
    else
    {
        $org = get_entity(get_input('org_guid'));
        if ($org)
        {
            org_page_not_found($org);
        }
        else
        {
            not_found();
        }
    }

?>