<?php
    $widget = $vars['widget'];
    
    ob_start();
    
    echo view('widgets/network_edit_relationship', array(
        'header' => __("network:edit_memberships"), 
        'widget' => $widget,
        'type' => OrgRelationship::Membership,
        'add_action' => 'add_membership',
        'delete_action' => 'delete_membership',
        'empty_message' => __("network:no_memberships"),
        'add_message' => __('network:add_membership'),        
        'confirm_delete_message' => __('network:confirm_delete_membership')
    ));
        
    echo view('widgets/network_edit_relationship', array(
        'header' => __("network:edit_members"), 
        'widget' => $widget,
        'type' => OrgRelationship::Member,
        'add_action' => 'add_member',
        'delete_action' => 'delete_member',
        'empty_message' => __("network:no_members"),
        'add_message' => __('network:add_member'),        
        'confirm_delete_message' => __('network:confirm_delete_member')
    ));    
    
    echo view('widgets/network_edit_relationship', array(
        'header' => __("network:edit_partnerships"), 
        'widget' => $widget,
        'type' => OrgRelationship::Partnership,
        'add_action' => 'add_partnership',
        'delete_action' => 'delete_partnership',
        'empty_message' => __("network:no_partnerships"),
        'add_message' => __('network:add_partnership'),        
        'confirm_delete_message' => __('network:confirm_delete_partnership')
    ));        
    
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
       'widget' => $widget,
       'body' => $content,
       'noSave' => true
       
    ));
        
?>