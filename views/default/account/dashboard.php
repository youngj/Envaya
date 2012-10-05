<?php
    $user = $vars['user'];

    if (Permission_EditUserSite::has_for_entity($user))
    {    
        $news = Widget_News::get_for_entity($user);
        if ($news)
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
    }
    
    echo view('section', array(
        'header' => __("dashboard:links"), 
        'content' => view('account/links', array('user' => $user))
    ));
    