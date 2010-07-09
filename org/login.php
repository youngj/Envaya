<?php

    set_context('login');

    $username = get_input('username');

    $title = elgg_echo("login");
    $body = elgg_view_layout('one_column_padded',
        elgg_view_title($title, array('org_only' => true)),
        elgg_view("account/forms/login", array('username' => $username)));

    page_draw($title, $body);
