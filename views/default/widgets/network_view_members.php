<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $offset = (int) get_input('offset');
    $limit = 50;

    $query = $org->query_network_members()->limit($limit, $offset);
    
    $count = $query->count();
    $entities = $query->filter();
    
    echo view('paged_list', array(
        'entities' => $entities,
        'count' => $count,
        'offset' => $offset,
        'limit' => $limit,
    ));        
        
    if (!$count)
    {
        echo __("network:no_members");
    }
