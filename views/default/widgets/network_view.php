<?php 
    $widget = $vars['widget'];

    $types = array(
        OrgRelationship::Partnership,
        OrgRelationship::Membership,
        OrgRelationship::Member,
    );
    
    ob_start();
        
    foreach ($types as $type)
    {
        echo view('widgets/network_view_relationship_group', array(
            'widget' => $widget,
            'type' => $type,
        ));
    }        
    
    $content = ob_get_clean();
    if ($content)
    {
        echo $content;
    }
    else
    {
        echo "<div class='section_content padded'>".__('network:empty')."</div>";
    }