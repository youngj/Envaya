<?php
    $user = $vars['user'];

    if ($user instanceof Organization)
    {    
        if (Permission_ViewUserSite::has_for_entity($user)
            && Session::is_logged_in() && !$user->equals(Session::get_logged_in_user()))
        {
            echo view("org/comm_box", array('org' => $user));
        }

        if (@$vars['show_next_steps'])
        {
            echo view("org/todo_message", array('org' => $user));
        }
    }
    echo SessionMessages::view_all();