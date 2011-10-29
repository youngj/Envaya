<?php
    $user = $vars['user'];

    $news = $user->get_widget_by_class('News');
    
    if ($news->is_enabled())
    {
        echo view('section', array(
            'header' => __("widget:news:add_update"), 
        ));                
        echo view('news/add_post', array('widget' => $news));
    }

    echo view('section', array(
        'header' => __("dashboard:edit_widgets"), 
        'content' => view('account/edit_widget_links', array('user' => $user))
    ));
      
    echo view('section', array(
        'header' => __("dashboard:links"), 
        'content' => view('account/dashboard_links', array('user' => $user))
    ));