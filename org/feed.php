<?php
                      
    $title = elgg_echo("feed:title");       

    $area = elgg_view("org/feed");

    $heading = elgg_view('page/simpleheading', array('title' => $title));
    $body = elgg_view_layout('one_column', $heading, $area);    

    page_draw($title, $body);

?>