<?php
    $pageName = get_input('page_name');       
    $area = elgg_view("page/$pageName");    
    $title = elgg_echo("$pageName:title");
    $body = elgg_view_layout('one_column_padded', elgg_view_title($title), $area);        
    page_draw($title, $body);