<?php
    $org = $vars['org'];
    
    echo view('widgets/available_list', array(
        'container' => $org, 
        'mode' => 'page',
        'add_link_text' => __('widget:add_link'),
        'add_link_url' => "{$org->get_url()}/add_page",
    ));
    