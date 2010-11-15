<?php
    $org = $vars['org'];
    
    echo view('section', array(
        'header' => __("dashboard:add_update"), 
        'content' => view('org/addPost', array('org' => $org))
    ));

    echo view('section', array(
        'header' => __("dashboard:edit_widgets"), 
        'content' => view('org/edit_widget_links', array('org' => $org))
    ));
   
    echo view('section', array(
        'header' => __("dashboard:links"), 
        'content' => view('org/dashboard_links', array('org' => $org))
    ));