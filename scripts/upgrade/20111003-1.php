<?php

    require_once "start.php";      
    
    $news = new SMS_Service_News();
    
    foreach (UserPhoneNumber::query()->filter() as $user_phone_number)
    {
        $user_guid = $user_phone_number->user_guid;
        $phone_number = $user_phone_number->phone_number;
        
        $state = $news->get_state($phone_number);
   
        if (!$state->user_guid)
        {
            $state->user_guid = $user_guid;
            $state->save();
            
            error_log("{$phone_number} : {$user_guid}");
        }
    }