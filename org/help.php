<?php
    gatekeeper();
    set_context('editor');
    
    $title = elgg_echo("help:title");       

    $area = elgg_view("org/help");

    $body = elgg_view_layout('one_column_padded', elgg_view_title($title), $area);    

    page_draw($title, $body);
    