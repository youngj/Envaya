<?php

require_once "scripts/cmdline.php";
require_once "start.php";

foreach (SMS_AppState::query()
    ->where('active = 1')
    ->where('time_updated < ?', timestamp() - 3660)
    ->filter() as $app_state)
{
    $app_state->active = false;
    
    $diff = timestamp() - $app_state->time_updated;
    
    error_log("{$app_state->phone_number} is inactive for $diff seconds");
    
    SMS_AppState::send_alert("Phone inactive", "{$app_state->phone_number} has not connected to server for $diff seconds");
        
    $app_state->save();        
}                