<?php
    $relationship = $vars['relationship'];
    $widget = $vars['widget'];
    $invitedEmail = $vars['invited_email'];
    
    $subject_name = $relationship->get_subject_name();

    $org = $relationship->get_container_entity();
    
    echo sprintf(__('email:salutation', $org->language), $subject_name);
    echo "\n\n";        
    
    echo strtr(__('network:invite_notify_info', $org->language), array(
        '{name}' => $org->name, 
        '{subject}' => $subject_name, 
        '{type}' => $relationship->msg('header', $org->language),
        '{url}' => abs_url($widget->get_url()),
    ));
    echo "\n\n";
           
    echo view('emails/network_relationship_invite_link', $vars);