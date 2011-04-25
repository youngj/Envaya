<?php

    require_once "scripts/cmdline.php";
    require_once "engine/start.php";

    $feeds = ExternalFeed::query()->filter();
    
    foreach ($feeds as $feed)
    {
        $feed->update();
    }   