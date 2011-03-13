<?php
    $widget = $vars['widget'];
    
    ob_start();
    
    echo view('section', array('header' => __("network:edit_memberships"), 
        'content' => view('widgets/network_edit_memberships', array('widget' => $widget))    
    ));    
        
    echo view('section', array('header' => __("network:edit_members"), 
        'content' => view('widgets/network_edit_members', array('widget' => $widget))    
    ));    
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
       'widget' => $widget,
       'body' => $content,
       'noSave' => true
       
    ));
        
?>