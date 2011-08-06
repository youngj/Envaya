<?php
    $relationship = $vars['relationship'];
    $invitedEmail = $vars['invited_email'];

    $org = $relationship->get_container_entity();

    echo __('home:description_africa', $org->language);
    echo "\n\n";    
     
    // include the original view overridden by this one
    include_view('emails/network_relationship_invite_link', 'default', $vars);
     
    echo __('network:invite_learn_more', $org->language);
    echo "\n";
    echo abs_url("/envaya/page/help");
