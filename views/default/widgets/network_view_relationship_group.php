<?php
    $widget = $vars['widget'];
    $type = $vars['type'];
    $header = OrgRelationship::msg($type, 'header');
    
    $org = $widget->get_container_entity();
    
    $query = $org->query_relationships()
        ->where('`type` = ?', $type)
        ->where('approval & ? > 0', OrgRelationship::SelfApproved);
    
    $entities = $query->filter();    
        
    if (sizeof($entities) > 0)
    {
        echo view('section', array('header' => $header, 
            'content' => view('paged_list', array(
                'entities' => $entities,
                'separator' => "<div style='clear:both;margin:10px 0px;' class='separator'></div>"
            ))
        ));
    }