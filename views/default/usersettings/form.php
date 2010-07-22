<?php

    $form_body = elgg_view("usersettings/user", array('entity' => $vars['entity']));

    echo elgg_view('input/form',
        array('enctype' => 'multipart/form-data',  'action' => "{$vars['entity']->getURL()}/settings/save",
        'body' => $form_body));
?>