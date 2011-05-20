<?php

    require_once "scripts/cmdline.php";
    require_once "engine/start.php";

    $time = time();
    
    // assume 'stuck' feeds older than this time are not actually queued/updating (maybe the process died)
    $recent = $time - 60 * 30; 
    
    $feeds = ExternalFeed::query()
        ->where('time_next_update < ?', $time)
        ->where('update_status = ? OR (update_status = ? AND time_queued < ?) OR (update_status = ? AND time_update_started < ?)', 
            ExternalFeed::Idle, ExternalFeed::Queued, $recent, ExternalFeed::Updating, $recent)
        ->order_by('time_next_update')    
        ->filter();
    
    foreach ($feeds as $feed)
    {
        $feed->queue_update();
    }   