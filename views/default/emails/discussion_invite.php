<?php
    $topic = $vars['topic'];
    $org = $topic->get_root_container_entity();
        
    echo __('email:generic_salutation');        
    echo "\n\n";
    echo strtr(__('discussions:invite_message'), array(
        '{name}' => $org->name, '{topic}' => $topic->subject
    ));
    echo "\n\n";
    echo __('discussions:invite_message2');
    echo "\n";    
    echo $topic->get_url();
