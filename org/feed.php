<?php
                      
    $title = elgg_echo("feed:title");       

    $area = elgg_view("org/feed");

    $body = elgg_view_layout('one_column', elgg_view_title($title), $area);    

    page_draw($title, $body);

?>