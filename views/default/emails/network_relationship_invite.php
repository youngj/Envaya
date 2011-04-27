<?php
    $relationship = $vars['relationship'];
    $widget = $vars['widget'];
    $invitedEmail = $vars['invited_email'];
    
    $base_url = Config::get('url');
    $subject_name = $relationship->get_subject_name();

    $org = $relationship->get_container_entity();
    
    echo sprintf(__('email:salutation', $org->language), $subject_name);
    echo "\n\n";        
    
    $tr = array(
        '{name}' => $org->name, 
        '{subject}' => $subject_name, 
        '{type}' => $relationship->msg('header', $subject->language),
        '{url}' => $widget->get_url(),
    );
    
    
    echo strtr(__('network:invite_notify_info', $org->language), $tr);
    echo "\n\n";
    
    echo __('home:description_africa', $org->language);
    echo "\n\n";    
       
    echo __('network:invite_sign_up', $org->language);
    echo "\n";
    echo "{$base_url}org/new?invite={$invitedEmail->invite_code}";
    echo "\n\n";
        
    echo __('network:invite_learn_more', $org->language);
    echo "\n";
    echo "{$base_url}envaya/page/why";
