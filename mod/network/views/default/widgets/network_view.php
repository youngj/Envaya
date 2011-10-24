<?php 
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $types = array(
        Relationship::Partnership,
        //Relationship::Membership,
        //Relationship::Member,
    );
    
    ob_start();
          
    foreach ($types as $type)
    {
        echo view('widgets/network_view_relationship_group', array(
            'widget' => $widget,
            'type' => $type,
        ));
    }
    
    echo view('widgets/network_view_feed', array(
        'widget' => $widget,
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