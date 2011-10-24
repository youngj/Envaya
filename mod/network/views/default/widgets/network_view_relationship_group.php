<?php
    $widget = $vars['widget'];
    $type = $vars['type'];
    
    $org = $widget->get_container_entity();
    
    $query = Relationship::query_for_user($org)
        ->where('`type` = ?', $type);
    
    $entities = $query->filter();    
        
    if (sizeof($entities) > 0)
    {
        echo view('section', array(
            'header' => Relationship::msg_for_type($type, 'header'), 
            'content' => view('paged_list', array(
                'entities' => $entities,
                'separator' => "<div style='clear:both;margin:10px 0px;' class='separator'></div>"
            ))
        ));
    }