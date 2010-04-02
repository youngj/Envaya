<?php
    $area = elgg_view("page/home");    
    $heading = elgg_view("page/homeheading");
    $title = elgg_echo("home:title");
    $body = elgg_view_layout('one_column', $heading, $area);        
    page_draw($title, $body);
