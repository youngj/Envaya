<?php
    if (get_input("__topbar") != "0")
    {
        $org = $vars['org'];
    
        if (Session::isadminloggedin())
        {
            echo view("admin/org_actions", array('org' => $org));
        }

        if ($org->can_view() && Session::isloggedin() && Session::get_loggedin_userid() != $org->guid)
        {
            echo view("org/comm_box", array('org' => $org));
        }

        if (@$vars['show_next_steps'])
        {
            echo view("org/todo_message", array('org' => $org));
        }
    }            
    echo SessionMessages::view_all();