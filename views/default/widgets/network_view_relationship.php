<?php
    $widget = $vars['widget'];
    $relationshipType = $vars['type'];
    $header = $vars['header'];
    
    $org = $widget->get_container_entity();
    
    $offset = (int) get_input('offset');
    $limit = 50;

    $query = $org->query_relationships()->where('`type` = ?', $relationshipType)->limit($limit, $offset);
    
    $count = $query->count();
    $entities = $query->filter();    
        
    if ($count)
    {
        echo view('section', array('header' => $header, 
            'content' => view('paged_list', array(
                'entities' => $entities,
                'count' => $count,
                'offset' => $offset,
                'limit' => $limit,
            ))
        ));
    }