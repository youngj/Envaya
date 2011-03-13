<?php 
    $widget = $vars['widget'];
    
    echo view('section', array('header' => __("network:memberships"), 
        'content' => view('widgets/network_view_memberships', array('widget' => $widget))    
    ));    
        
    echo view('section', array('header' => __("network:members"), 
        'content' => view('widgets/network_view_members', array('widget' => $widget))    
    ));    

    echo view('section', array('header' => __("network:partnerships"), 
        'content' => view('widgets/network_view_partnerships', array('widget' => $widget))    
    ));    
    