<?php

    $item = $vars['item'];
    $mode = $vars['mode'];    
    
    $update = $item->get_subject_entity();
    $user = $item->get_user_entity();
    
    $count = @$item->args['count'] ?: 1;

    echo view('feed/snippet', array(
        'thumbnail_url' => $update->has_image() ? $update->thumbnail_url : '',
        'link_url' => rewrite_to_current_domain($user->get_url() . "/news?end={$update->guid}"),
        'title' => __('widget:news:items'),
        'mode' => $vars['mode'],
        'org' => $item->get_user_entity(),
        'heading_format' => sprintf(__('feed:news_multi'), $count),        
        'content' => $update->render_content()
    ));    
