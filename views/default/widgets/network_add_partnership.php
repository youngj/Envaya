<?php
    $widget = $vars['widget'];
    
    echo view('widgets/network_add_relationship', array(
        'widget' => $widget, 
        'action' => 'add_partnership',
        'header' => __("network:add_partnership"),
        'can_add_unregistered' => __('network:can_add_unregistered_partner'),
        'confirm' => __('network:confirm_partner'),
        'not_shown' => __('network:org_not_shown'),
        'instructions' => __('network:add_partnership_instructions')
    ));
