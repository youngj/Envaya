<?php

    $widget = $vars['widget'];

    echo view('section', array(
        'header' => __("widget:news:add_update"), 
    ));        
    echo view('news/add_post', array('widget' => $widget));    
    
    echo view('widgets/news_edit_posts', array('widget' => $widget));    
    echo view('widgets/news_edit_feeds', array('widget' => $widget));    
                
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => '',
        'noSave' => true,
    ));    

?>
