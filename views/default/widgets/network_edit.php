<?php
    $widget = $vars['widget'];
    
    $types = array(
        OrgRelationship::Membership,
        OrgRelationship::Member,
        OrgRelationship::Partnership);
    
    ob_start();
    
    foreach ($types as $type)
    {
        echo view('widgets/network_edit_relationship_group', array(
            'widget' => $widget,
            'type' => $type,
        ));
    }
        
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
       'widget' => $widget,
       'body' => $content,
       'noSave' => true
       
    ));
        
?>