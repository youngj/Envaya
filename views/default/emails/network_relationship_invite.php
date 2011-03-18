<?php
    $relationship = $vars['relationship'];
    $widget = $vars['widget'];
    $invitedEmail = $vars['invited_email'];
    
    $base_url = Config::get('url');
    $subject_name = $relationship->get_subject_name();

    $org = $relationship->get_container_entity();
    
    echo sprintf(__('email:salutation', $org->language), $subject_name);
    echo "\n\n";        
    
    echo sprintf(__('network:invite_notify_info', $org->language), 
        $org->name, 
        $subject_name, 
        $relationship->__('header', $org->language),
        $widget->get_url()
    );
    echo "\n\n";
    
    $country_name = 'Tanzania';
    
    echo sprintf(__('home:description_developing', $org->language), $country_name);
    echo "\n\n";    
       
    echo sprintf(__('network:invite_sign_up', $org->language));
    echo "\n";
    echo "{$base_url}org/new?invite={$invitedEmail->invite_code}";
    echo "\n\n";
        
    echo __('network:invite_learn_more', $org->language);
    echo "\n";
    echo "{$base_url}envaya/page/why";
