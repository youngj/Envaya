<?php
    admin_gatekeeper();
    $user = get_user_by_username(get_input('username') ?: 'envaya');

    if ($user)
    {
        echo elgg_view('emails/reminder', array('org' => $user));
    }
    else
    {
        not_found();
    }
