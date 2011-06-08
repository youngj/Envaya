<?php
    $relationship = $vars['relationship'];
    $invitedEmail = $vars['invited_email'];
    
    $org = $relationship->get_container_entity();

    $base_url = Config::get('url');
    
    echo __('network:invite_sign_up', $org->language);
    echo "\n";
    echo "{$base_url}org/new?invite={$invitedEmail->invite_code}";
    echo "\n\n";
