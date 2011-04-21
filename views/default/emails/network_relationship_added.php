<?php
    $relationship = $vars['relationship'];
    $reverse = $vars['reverse'];
    $widget = $vars['widget'];

    $org = $relationship->get_container_entity();
    $subject = $relationship->get_subject_organization();
    
    echo sprintf(__('email:salutation', $subject->language), $subject->name);
    echo "\n\n";    
    
    echo sprintf(__('network:notify_added_info', $subject->language), 
        $org->name, 
        $subject->name, 
        $relationship->__('header', $subject->language)
    );
    
    echo "\n";
    echo $widget->get_url();
    echo "\n\n";
    
    if (!$reverse->is_self_approved())
    {    
        echo sprintf(__('network:notify_added_instructions', $subject->language), 
            $org->name, 
            $reverse->__('header', $subject->language)
        );
        
        echo "\n";
        
        $subjectWidget = $subject->get_widget_by_class('Network');
        echo  "{$subjectWidget->get_edit_url()}?action=approve&guid={$reverse->guid}&username={$subject->username}";
        echo "\n\n";        
    }