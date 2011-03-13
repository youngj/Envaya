<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $offset = (int) get_input('offset');
    $limit = 50;

    $query = $org->query_network_memberships()->limit($limit, $offset);
   
    $count = $query->count();
    $memberships = $query->filter();
    
    $networks = array();
    foreach ($memberships as $membership)
    {
        $networks[] = $membership->get_container_entity();
    }
    
    echo view('paged_list', array(
        'entities' => $networks,
        'count' => $count,
        'offset' => $offset,
        'limit' => $limit,
    ));        
        
    if (!$count)
    {
        echo __("network:no_members");
    }
