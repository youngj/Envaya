<?php

    $form_body = view("usersettings/user", array('entity' => $vars['entity']));
    
    echo view('input/form',
        array('enctype' => 'multipart/form-data', 'action' => secure_url("{$vars['entity']->get_url()}/settings"),
        'body' => $form_body));
?>