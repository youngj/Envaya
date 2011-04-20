<?php
    $widget = $vars['widget'];
   
    $content = view('section', array(
        'header' =>  __('widget:edit_sections'), 
        'content' =>  view('widgets/available_list', array(
            'container' => $widget, 
            'mode' => 'home_section',
            'add_link_text' => __('widget:add_section_link'),
            'add_link_url' => "{$widget->get_base_url()}/add_widget",        
        ))
    )); 
    
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content,
        'noSave' => true,
    ));
?>