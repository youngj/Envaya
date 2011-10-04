<?php
    require_once "start.php";

    foreach (SMS_State::query()->filter() as $state)
    {
        $user_guid = $state->get('user_guid');
        if ($state->user_guid == 0 && $user_guid)
        {
            $state->user_guid = $user_guid;
            $state->set('user_guid', null);
            $state->save();            
        }
        
        error_log("{$state->phone_number} : {$state->user_guid}");        
    }