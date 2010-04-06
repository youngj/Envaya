<?php    

    set_context('login');
    
    $title = elgg_echo("login");
    $heading = elgg_view('page/simpleheading', array('title' => $title, 'org_only' => true));
    $body = elgg_view_layout('one_column_padded', $heading, elgg_view("account/forms/login"));
           
    page_draw($title, $body);
