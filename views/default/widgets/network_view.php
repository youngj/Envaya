<?php 
    $widget = $vars['widget'];
        
    ob_start();
        
    echo view('widgets/network_view_relationship', array(
        'header' => __("network:memberships"),
        'widget' => $widget,
        'type' => OrgRelationship::Membership,
    ));
        
    echo view('widgets/network_view_relationship', array(
        'header' => __("network:members"),
        'widget' => $widget,
        'type' => OrgRelationship::Member,
    ));

    echo view('widgets/network_view_relationship', array(
        'header' => __("network:partnerships"),
        'widget' => $widget,
        'type' => OrgRelationship::Partnership,
    ));   
    
    $content = ob_get_clean();
    if ($content)
    {
        echo $content;
    }
    else
    {
        echo "<div class='section_content padded'>".__('network:empty')."</div>";
    }