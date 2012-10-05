<?php
    $widget = $vars['widget'];
    
    ob_start();
    echo "<div class='section_content padded'>";
    echo view('widgets/edit_content', $vars);
    
    echo view('input/submit', array(
        'value' => __('savechanges')
    ));
    echo "</div>";
    
    echo view('section', array(
        'header' =>  __('widget:edit_sections'), 
        'content' =>  view('widgets/available_list', array(
            'container' => $widget, 
            'mode' => 'home_section',
            'add_link_text' => __('widget:add_section_link'),
            'add_link_url' => "{$widget->get_base_url()}/add",        
        ))
    )); 
    
    $content = ob_get_clean();
    
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content,
        //'noSave' => true,
    ));
?>