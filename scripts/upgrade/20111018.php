<?php
    require_once "start.php";      
    
    foreach (Organization::query()
        ->where('country = ?','tz')
        ->where('approval = 1')
        ->filter() as $org)
    {
        $primary_phone = $org->get_primary_phone_number();
        
        if ($primary_phone)
        {
            $s = $org->init_batch_sms_subscription($primary_phone);
            if ($s)
            {
                error_log("{$org->username} : subscribed $primary_phone");
            }
            else
            {
                error_log("{$org->username} : can't subscribe $primary_phone");
            }
        }
    }