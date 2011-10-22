<?php
    require_once "start.php";      
    
    foreach (Organization::query()
        ->filter() as $org)
    {   
        error_log("{$org->username}");
        $org->init_admin_subscriptions();
    }