<?php
    $topic = $vars['topic'];
    $org = $topic->get_root_container_entity();
        
    echo __('discussions:invite_salutation');        
    echo "\n\n";
    echo sprintf(__('discussions:invite_message'), $org->name, $topic->subject);
    echo "\n\n";
    echo __('discussions:invite_message2');
    echo "\n";    
    echo $topic->get_url();
