<?php
    admin_gatekeeper();

    set_context('editor');
    set_theme('editor');

    $title = elgg_echo('email:send');

    $area1 = elgg_view('org/contact');

    $body = elgg_view_layout("one_column_wide", elgg_view_title($title), $area1);

    page_draw($title,$body);
