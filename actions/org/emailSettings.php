<?php

    $email = get_input('email');
    $code = get_input('code');
    $notify_days = get_input('notify_days');
    $users = get_users_by_email($email);

    foreach ($users as $user)
    {
        $user->notify_days = $notify_days;
        $user->save();

        system_message(elgg_echo('user:notification:success'));
    }

    forward("/");