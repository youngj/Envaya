<?php

    require_once "start.php";      
    
    $root = UserScope::get_root();
    
    $users = User::query()
        ->where('container_guid = 0 or container_guid = guid or container_guid = ?', $root->guid)
        ->filter();
    
    foreach ($users as $user)
    {
        $user->update_scope();
        $user->save();
    }
    
    