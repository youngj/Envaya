<?php
    $relationship = $vars['relationship'];
    $invitedEmail = $vars['invited_email'];
    
    $org = $relationship->get_container_entity();

    echo __('network:invite_sign_up', $org->language);
    echo "\n";
    echo abs_url("/org/new?invite={$invitedEmail->invite_code}");
    echo "\n\n";
