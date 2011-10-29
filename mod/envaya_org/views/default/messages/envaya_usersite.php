<?php
    $user = $vars['user'];

    if (($user instanceof Organization) && get_input("__topbar") != "0")
    {
        if (Permission_UseAdminTools::has_for_entity($user))
        {
            echo view("admin/org_actions", array('org' => $user));
        }

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