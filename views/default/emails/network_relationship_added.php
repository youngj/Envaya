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
    
    if (!$reverse->is_self_approved())
    {    
        $tr['{type}'] = $reverse->msg('header', $subject->language);
        
        echo strtr(__('network:notify_added_instructions', $subject->language), $tr);
        
        echo "\n";
        
        $subjectWidget = $subject->get_widget_by_class('Network');
        echo  "{$subjectWidget->get_edit_url()}?action=approve&guid={$reverse->guid}&username={$subject->username}";
        echo "\n\n";        
    }