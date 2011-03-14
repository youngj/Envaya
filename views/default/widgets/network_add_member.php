<?php
    $widget = $vars['widget'];
    
    echo view('widgets/network_add_relationship', array(
        'widget' => $widget, 
        'action' => 'add_member',
        'header' => __("network:add_member"),
        'can_add_unregistered' => __('network:can_add_unregistered_member'),
        'confirm' => __('network:confirm_member'),
        'not_shown' => __('network:org_not_shown'),
        'instructions' => __('network:add_member_instructions')
    ));
