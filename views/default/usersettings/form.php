<?php

    $form_body = elgg_view("usersettings/user", array('user' => $vars['user']));

    echo elgg_view('input/form',
        array('enctype' => 'multipart/form-data',  'action' => "{$vars['url']}action/save_settings",
        'body' => $form_body));
?>