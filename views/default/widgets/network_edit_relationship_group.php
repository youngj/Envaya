<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $type = $vars['type'];
    $empty = OrgRelationship::msg($type, 'empty');
    $add = OrgRelationship::msg($type, 'add');
    $header = OrgRelationship::msg($type, 'header');
    
    $offset = (int) get_input('offset');
    $limit = 5;
    $query = $org->query_relationships()->where('`type` = ?', $type)->limit($limit, $offset);
    
    $count = $query->count();
    $relationships = $query->filter();
    
    $addUrl = "{$widget->get_edit_url()}?action=add_relationship&type={$type}";
    
    if ($count)
    {        
        ob_start();
    
        $elements = array();
        
        foreach ($relationships as $relationship)
        {
            $elements[] = view('widgets/network_edit_relationship_group_item', array(
                'relationship' => $relationship,
                'widget' => $widget,
            ));
        }
        
        echo view('paged_list', array(
            'elements' => $elements,
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit,
            'separator' => "<div class='separator'></div>"
        ));
        
        $content = ob_get_clean();
    
        echo view('section', array('header' => $header, 'content' => $content));
    }    
    
