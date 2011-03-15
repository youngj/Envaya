<?php
    $widget = $vars['widget'];
    $type = $vars['type'];
    $header = OrgRelationship::msg($type, 'header');
    
    $org = $widget->get_container_entity();
    
    $offset = (int) get_input('offset');
    $limit = 50;

    $query = $org->query_relationships()
        ->where('`type` = ?', $type)
        ->where('approval & ? > 0', OrgRelationship::SelfApproved)
        ->limit($limit, $offset);
    
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
                'separator' => "<div style='clear:both;margin:10px 0px;' class='separator'></div>"
            ))
        ));
    }