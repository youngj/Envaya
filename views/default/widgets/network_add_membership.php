<?php
    $widget = $vars['widget'];
    
    echo view('widgets/network_add_relationship', array(
        'widget' => $widget, 
        'action' => 'add_membership',
        'header' => __('network:add_membership'),
        'name_label' => __('network:network_name'),
        'confirm' => __('network:confirm_network'),
        'not_shown' => __('network:network_not_shown'),
        'can_add_unregistered' => __('network:can_add_unregistered_network'),
        'instructions' => __('network:add_membership_instructions')
    ));
