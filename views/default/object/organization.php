<?php
    $entity = $vars['entity'];
    
    $icon = view('org/icon', array('org' => $entity));

    $info = "<div><b><a href='{$entity->get_url()}'>" . escape($entity->name) . "</a>" . 
        (!$entity->is_approved() ? (" (" . __('approval:notapproved') .") ") : "") .
        "</b></div>";
        
    echo view('search/listing',array(
        'icon' => "<a href='{$entity->get_url()}'>$icon</a>", 
        'info' => $info
    ));
