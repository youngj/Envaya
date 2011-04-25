<?php 
    $widget = $vars['widget'];    
    
    $query = $widget->query_published_widgets()
        ->order_by('time_published desc, guid desc');
        
    $posts = $query->limit(10)->filter(); 

    foreach ($posts as $post)
    {
        echo view('widgets/item', array('widget' => $post));
    }