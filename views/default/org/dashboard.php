<?php
    $org = $vars['org'];

    if ($org->get_widget_by_class('News')->is_active())
    {
        echo view('section', array(
            'header' => __("dashboard:add_update"), 
            'content' => view('news/add_post', array('org' => $org))
        ));
    }

    echo view('section', array(
        'header' => __("dashboard:edit_widgets"), 
        'content' => view('org/edit_widget_links', array('org' => $org))
    ));
      
    echo view('section', array(
        'header' => __("dashboard:links"), 
        'content' => view('org/dashboard_links', array('org' => $org))
    ));