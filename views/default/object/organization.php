<?php
    $entity = $vars['entity'];
    
    $icon = view('graphics/icon', array(
        'entity' => $entity,
        'size' => 'small',
    ));

    $info = "<div><b><a href='{$entity->get_url()}'>" . escape($entity->name) . "</a>" . 
        (!$entity->is_approved() ? (" (" . __('approval:notapproved') .") ") : "") .
        "</b></div>";

    echo view('search/listing',array(
        'icon' => "<a href='{$entity->get_url()}'>$icon</a>", 
        'info' => $info
    ));
