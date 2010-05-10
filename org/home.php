<?php
    set_context('home');
    $area = elgg_view("page/home");
    $title = elgg_echo("home:title");
    $body = elgg_view_layout('one_column', elgg_echo('home:heading'), $area);
    page_draw($title, $body);
