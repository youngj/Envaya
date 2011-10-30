<?php
    $user = $vars['user'];

    if ($user instanceof Organization)
    {
        echo view("org/todo_message", array('org' => $user));
    }