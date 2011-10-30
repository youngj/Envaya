<?php
    $user = $vars['user'];
    if (Permission_UseAdminTools::has_for_entity($user))
    {
        echo view("admin/user_actions", array('user' => $user));
    }

    echo SessionMessages::view_all();