<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $type = $vars['type'];
    
    $query = Relationship::query_for_user($org)->where('`type` = ?', $type);
    
    $relationships = $query->filter();
      
    if (sizeof($relationships) > 0)
    {        
        echo view('section', array(
            'header' => Relationship::msg_for_type($type, 'header'), 
            'content' => view('paged_list', array(
                'items' => array_map(function($relationship) use ($widget) {
                     return view('widgets/network_edit_relationship_group_item', array(
                        'relationship' => $relationship,
                        'widget' => $widget,
                    ));
                }, $relationships),
                'separator' => "<div class='separator'></div>"
            ))
        ));
    }    
    