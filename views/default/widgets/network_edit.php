<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $types = array(
        OrgRelationship::Partnership,
        //OrgRelationship::Membership,
        //OrgRelationship::Member,
        );
    
    ob_start();
    
    echo "<div class='section_content padded'>";
    
    echo "<div class='instructions'>";
    echo "<p>".sprintf(__('network:instructions'), escape($org->name))."</p>";
    echo "<p>".__('network:instructions_2')."</p>";
    echo "</div>";
    
    //echo "<ul>";
    foreach ($types as $type)
    {
        //echo "<li>";
        echo "<div style='font-weight:bold;text-align:center'><a href='{$widget->get_edit_url()}?action=add_relationship&type={$type}'>"
            .OrgRelationship::msg($type, 'add_header')."</a></div>";
        //echo "</li>";
    }
    //echo "</ul>";
    
    echo "</div>";    
    
    foreach ($types as $type)
    {
        echo view('widgets/network_edit_relationship_group', array(
            'widget' => $widget,
            'type' => $type,
        ));
    }
        
    $content = ob_get_clean();

    if ($widget->guid || !$org->query_relationships()->is_empty())
    {
        echo view("widgets/edit_form", array(
           'widget' => $widget,
           'body' => $content,
           'noSave' => true          
        ));
    }
    else
    {
        echo $content."<br />";
    }
        
?>