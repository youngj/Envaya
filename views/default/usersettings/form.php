<?php

    $form_body = elgg_view("usersettings/user", array('user' => $vars['user']));

    echo elgg_view('input/form',
        array('enctype' => 'multipart/form-data',  'action' => "{$vars['user']->getURL()}/settings/save",
        'body' => $form_body));
?>