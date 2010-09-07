<?php

    $item = $vars['item'];
    $mode = $vars['mode'];    
    $update = $item->get_subject_entity();

    echo view('feed/snippet', array(
        'thumbnail_url' => $update->has_image() ? $update->thumbnail_url : '',
        'link_url' => rewrite_to_current_domain($update->get_url()),
        'title' => __('widget:news:item'),
        'mode' => $vars['mode'],
        'org' => $item->get_user_entity(),
        'heading_format' => __('feed:news'),        
        'content' => $update->render_content()
    ));    
