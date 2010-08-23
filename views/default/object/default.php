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
    if ($vars['entity']->can_edit())
    {
        $controls .= " (".view('output/confirmlink', array(
            'text' => __('delete'),
            'is_action' => true,
            'href' => "admin/delete_entity?guid={$vars['entity']->guid}"
        )).")";        
    }

    $info = "<div><p><b><a href=\"" . $vars['entity']->get_url() . "\">" . escape($title) . "</a></b> $controls </p></div>";

    if (get_input('search_viewtype') == "gallery") {

        $icon = "";

    }

    $owner = $vars['entity']->get_owner_entity();
    $ownertxt = __('unknown');

    $info = "<span>$info</span>";
    $icon = "<span>$icon</span>";

    echo view('search/listing',array('icon' => $icon, 'info' => $info));
