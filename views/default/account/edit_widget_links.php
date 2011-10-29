<?php
    $user = $vars['user'];
    
    echo view('widgets/available_list', array(
        'container' => $user, 
        'mode' => 'page',
        'add_link_text' => __('widget:add_link'),
        'add_link_url' => "{$user->get_url()}/add_page",
    ));
    