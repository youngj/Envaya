<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $type = $vars['type'];
    $empty = OrgRelationship::msg($type, 'empty');
    $add = OrgRelationship::msg($type, 'add');
    $header = OrgRelationship::msg($type, 'header');
    
    $query = $org->query_relationships()
        ->where('`type` = ?', $type);
    
    $relationships = $query->filter();
    
    $addUrl = "{$widget->get_edit_url()}?action=add_relationship&type={$type}";
    
    if (sizeof($relationships) > 0)
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
            'separator' => "<div class='separator'></div>"
        ));
        
        $content = ob_get_clean();
    
        echo view('section', array('header' => $header, 'content' => $content));
    }    
    
