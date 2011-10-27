<?php
    $widget = $vars['widget'];
    $type = $vars['type'];
    
    $org = $widget->get_container_entity();
    
    $query = Relationship::query_for_user($org)
        ->where('`type` = ?', $type);
    
    $relationships = $query->filter();    
        
    if (sizeof($relationships) > 0)
    {
        echo view('section', array(
            'header' => Relationship::msg_for_type($type, 'header'), 
            'content' => view('paged_list', array(
                'items' => array_map(function($rel) {
                    return view('widgets/network_view_relationship', array('relationship' => $rel));
                }, $relationships),
                'separator' => "<div style='clear:both;margin:10px 0px;' class='separator'></div>"
            ))
        ));
    }