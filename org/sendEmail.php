<?php

    admin_gatekeeper();

    set_context('editor');
    set_theme('editor');

    $title = elgg_echo('email:send');

    $org = get_user_by_username(get_input('username'));

    if ($org)
    {
        $area1 = elgg_view('org/sendEmail', array('org' => $org, 'from' => get_input('from')));

        $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);

        page_draw($title,$body);
    }
    else
    {
        not_found();
    }
