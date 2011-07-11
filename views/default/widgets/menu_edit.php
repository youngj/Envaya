<?php
    $widget = $vars['widget'];
    
    echo view('widgets/generic_edit', array('widget' => $widget));    
   
    echo view('section', array(
        'header' =>  __('widget:edit_sections'), 
        'content' =>  view('widgets/available_list', array(
            'container' => $widget, 
            'add_link_text' => __('widget:add_section_link'),
            'add_link_url' => "{$widget->get_base_url()}/add",        
        ))
    )); 
    
