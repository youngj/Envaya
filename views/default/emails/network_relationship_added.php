<?php
    $relationship = $vars['relationship'];
    $reverse = $vars['reverse'];
    $widget = $vars['widget'];

    $org = $relationship->get_container_entity();
    $subject = $relationship->get_subject_organization();
    
    echo sprintf(__('email:salutation', $subject->language), $subject->name);
    echo "\n\n";    
    
    $tr = array(
        '{name}' => $org->name, 
        '{subject}' => $subject->name, 
        '{type}' => $relationship->msg('header', $subject->language)
    );
    
    echo strtr(__('network:notify_added_info', $subject->language), $tr);
    
    echo "\n";
    echo $widget->get_url();
    echo "\n\n";
    
    if (!$reverse)
    {    
        $reverse_type = OrgRelationship::get_reverse_type($relationship->type);        
        $tr['{type}'] = OrgRelationship::msg_for_type($reverse_type, 'header', $subject->language);
        
        echo strtr(__('network:notify_added_instructions', $subject->language), $tr);        
        echo "\n";
        
        $subjectWidget = $subject->get_widget_by_class('Network');
        echo  "{$subjectWidget->get_edit_url()}?action=add_relationship&type={$reverse_type}&org_guid={$org->guid}&username={$subject->username}";
        echo "\n\n";        
    }