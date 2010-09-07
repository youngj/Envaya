<?php

    $item = $vars['item'];
    
    $widget = $item->get_subject_entity();
        
    echo view('feed/snippet', array(
        'thumbnail_url' => $widget->has_image() ? $widget->thumbnail_url : '',
        'link_url' => rewrite_to_current_domain($widget->get_url()),
        'title' => $widget->get_title(),
        'mode' => $vars['mode'],
        'org' => $item->get_user_entity(),
        'heading_format' => __('feed:edit_widget'),        
        'content' => $widget->render_content()
    ));
        