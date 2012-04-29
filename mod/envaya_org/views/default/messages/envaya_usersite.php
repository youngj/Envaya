<?php
    $user = $vars['user'];

    if ($user instanceof Organization)
    {    
        if (@$vars['show_next_steps'])
        {
            echo view("org/todo_message", array('org' => $user));
        }
    }
    echo SessionMessages::view_all();