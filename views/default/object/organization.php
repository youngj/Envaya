<?php
    
    $icon = view(
            'graphics/icon', array(
            'entity' => $vars['entity'],
            'size' => 'small',
        )
    );


    $title = $vars['entity']->title;
    if (!$title) $title = $vars['entity']->name;
    if (!$title) $title = get_class($vars['entity']);

    $controls = "";

    $info = "<div><b><a href=\"" . $vars['entity']->get_url() . "\">" . escape($title) . "</a>" . (!$vars['entity']->is_approved() ? (" (" . __('approval:notapproved') .") ") : "") . "</b> $controls</div>";

    if (get_input('search_viewtype') == "gallery") {

        $icon = "";

    } 

    $icon = "<a href=\"" . $vars['entity']->get_url() . "\">$icon</a>";

    echo view('search/listing',array('icon' => $icon, 'info' => $info));
