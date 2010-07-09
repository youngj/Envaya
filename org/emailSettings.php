<?php

    $email = get_input('e');
    $code = get_input('c');
    $users = get_users_by_email($email);

    $title = elgg_echo("user:notification:label");

    if ($email && $code == get_email_fingerprint($email) && sizeof($users) > 0)
    {
        $area1 = elgg_view('org/emailSettings', array('email' => $email, 'users' => $users));
    }
    else
    {
        $area1 = elgg_echo("user:notification:invalid");
    }
    $body = elgg_view_layout("one_column_padded", elgg_view_title($title), $area1);

    page_draw($title, $body);