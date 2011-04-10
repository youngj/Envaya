<?php
    $entity = $vars['entity'];
    $icon = view('graphics/icon', array(
        'entity' => $entity,
        'size' => 'small',
    ));

    $title = $entity->title ?: $entity->name ?: get_class($entity);

    $controls = "";
    if ($entity->can_edit())
    {
        $controls .= " (".view('output/confirmlink', array(
            'text' => __('delete'),
            'href' => "admin/delete_entity?guid={$entity->guid}"
        )).")";        
    }

    $info = "<div><p><b><a href='{$entity->get_url()}'>".escape($title)."</a></b> $controls </p></div>";

    echo view('search/listing',array('icon' => "<span>$icon</span>", 'info' => $info));
