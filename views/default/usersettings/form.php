<?php

    $form_body = view("usersettings/user", array('entity' => $vars['entity']));

    echo view('input/form',
        array('enctype' => 'multipart/form-data',  'action' => "{$vars['entity']->get_url()}/settings/save",
        'body' => $form_body));
?>