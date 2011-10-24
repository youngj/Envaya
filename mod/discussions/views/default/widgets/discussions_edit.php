<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
         
    ob_start();           
        
    echo view('widgets/discussions_edit_topics', array('widget' => $widget));   
        
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
       'widget' => $widget,
       'body' => $content,
       'noSave' => true          
    ));
